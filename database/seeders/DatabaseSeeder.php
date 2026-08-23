<?php

namespace Database\Seeders;

use App\Enums\BillingInterval;
use App\Enums\CourseStatus;
use App\Enums\OrganizationStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Subscription;
use App\Services\Contracts\AuditLogServiceInterface;
use App\Services\Contracts\CourseServiceInterface;
use App\Services\Contracts\OrganizationServiceInterface;
use App\Services\Contracts\PlanServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\Contracts\TransactionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faker = fake();

        $orgService = app(OrganizationServiceInterface::class);
        $userService = app(UserServiceInterface::class);
        $courseService = app(CourseServiceInterface::class);
        $planService = app(PlanServiceInterface::class);
        $subService = app(SubscriptionServiceInterface::class);
        $txService = app(TransactionServiceInterface::class);
        $auditService = app(AuditLogServiceInterface::class);

        // 1) system_admin (no organization)
        $systemAdmin = $userService->create([
            'name' => 'System Admin',
            'email' => 'admin@theacademy.test',
            'password' => 'password',
            'role' => UserRole::SystemAdmin,
            'organization_id' => null,
        ]);
        $systemAdmin->forceFill(['email_verified_at' => now()])->save();

        // 2) 2–3 organizations
        $orgCount = $faker->numberBetween(2, 3);
        $organizations = collect();

        for ($o = 0; $o < $orgCount; $o++) {
            $org = $orgService->create([
                'name' => $faker->unique()->company(),
                'address' => $faker->address(),
                'email' => $faker->unique()->companyEmail(),
                'phone' => $faker->phoneNumber(),
                'status' => $faker->boolean(85) ? OrganizationStatus::Active : OrganizationStatus::Suspended,
            ]);
            $organizations->push($org);

            // 3) per org: 1 org_admin, 2–3 teachers, 5–10 students
            $orgAdmin = $userService->create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => 'password',
                'role' => UserRole::OrgAdmin,
                'organization_id' => $org->id,
            ]);
            $orgAdmin->forceFill(['email_verified_at' => now()])->save();

            $teacherCount = $faker->numberBetween(2, 3);
            $teachers = collect();
            for ($t = 0; $t < $teacherCount; $t++) {
                $teacher = $userService->create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => 'password',
                    'role' => UserRole::Teacher,
                    'organization_id' => $org->id,
                ]);
                $teacher->forceFill(['email_verified_at' => now()])->save();
                $teachers->push($teacher);
            }

            $studentCount = $faker->numberBetween(5, 10);
            $students = collect();
            for ($s = 0; $s < $studentCount; $s++) {
                $student = $userService->create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => 'password',
                    'role' => UserRole::Student,
                    'organization_id' => $org->id,
                ]);
                $student->forceFill(['email_verified_at' => now()])->save();
                $students->push($student);
            }

            // 4) per teacher: 2–4 courses
            $orgCourses = collect();
            foreach ($teachers as $teacher) {
                $courseNum = $faker->numberBetween(2, 4);
                for ($c = 0; $c < $courseNum; $c++) {
                    $course = $courseService->create([
                        'organization_id' => $org->id,
                        'teacher_id' => $teacher->id,
                        'title' => ucfirst($faker->unique()->words(3, true)),
                        'description' => $faker->paragraph(),
                        'status' => $faker->boolean(80) ? CourseStatus::Published : CourseStatus::Draft,
                    ]);
                    $orgCourses->push($course);
                }
            }

            // 5) per org: 2–3 plans, each attached to 2–3 random courses
            $planCount = $faker->numberBetween(2, 3);
            $orgPlans = collect();
            for ($p = 0; $p < $planCount; $p++) {
                $courseIds = $orgCourses->isNotEmpty()
                    ? $orgCourses->random(min($faker->numberBetween(2, 3), $orgCourses->count()))->pluck('id')->all()
                    : [];

                $plan = $planService->create([
                    'organization_id' => $org->id,
                    'name' => ucfirst($faker->words(2, true)).' Plan',
                    'price' => $faker->randomFloat(2, 10, 100),
                    'billing_interval' => $faker->randomElement(BillingInterval::cases()),
                ], $courseIds);
                $orgPlans->push($plan);
            }

            // 6) subset of students: subscriptions to plans in their own org
            // ~60% of students get a subscription
            $subscribing = $students->shuffle()->take((int) ceil($students->count() * 0.6));
            $orgSubscriptions = collect();

            foreach ($subscribing as $student) {
                if ($orgPlans->isEmpty()) {
                    continue;
                }
                $plan = $orgPlans->random();

                // via Service so transaction + invoice + audit log are created
                $subscription = $subService->subscribe($student, $plan);
                $orgSubscriptions->push($subscription);

                // mostly active + a few trialing/canceled/past_due
                $roll = $faker->numberBetween(1, 100);
                if ($roll <= 10) {
                    $subService->updateStatus($subscription, SubscriptionStatus::Trialing);
                    $subscription->refresh();
                } elseif ($roll <= 20) {
                    $subService->updateStatus($subscription, SubscriptionStatus::PastDue);
                    $subscription->refresh();
                } elseif ($roll <= 30) {
                    $subService->cancel($student, $subscription);
                    $subscription->refresh();
                }

                // 7) per *active* subscription: 0–2 extra completed transactions (subscribe already created 1)
                if ($subscription->status === SubscriptionStatus::Active) {
                    $extra = $faker->numberBetween(0, 2);
                    for ($e = 0; $e < $extra; $e++) {
                        $txService->recordSubscriptionPayment($subscription, (float) $plan->price);
                    }
                }
            }

            // 8) handful of extra audit logs referencing subs/transactions
            $recentSubs = $orgSubscriptions->take(3);
            foreach ($recentSubs as $sub) {
                if ($faker->boolean(60)) {
                    $auditService->record(
                        $faker->boolean(70) ? $students->random() : $orgAdmin,
                        'subscription.viewed',
                        $sub,
                        null,
                        ['id' => $sub->id]
                    );
                }
            }
            // also audit for transactions
            foreach ($orgSubscriptions as $sub) {
                foreach ($sub->transactions()->take(1)->get() as $tx) {
                    if ($faker->boolean(40)) {
                        $auditService->record(
                            $sub->user,
                            'transaction.viewed',
                            $tx,
                            null,
                            ['id' => $tx->id]
                        );
                    }
                }
            }
        }

        // Global handful to guarantee at least a few audit_logs even on small seed
        $allSubs = Subscription::with('user')->get();
        if ($allSubs->isNotEmpty()) {
            for ($i = 0; $i < 4; $i++) {
                $sub = $allSubs->random();
                app(AuditLogServiceInterface::class)->record(
                    $sub->user,
                    fake()->randomElement(['subscription.updated', 'subscription.renewed', 'audit.sample']),
                    $sub,
                    ['status' => 'active'],
                    ['status' => $sub->status->value]
                );
            }
        }
    }
}
