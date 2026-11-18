<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_org_admin_can_view_subscriptions_but_cannot_subscribe(): void
    {
        $org = Organization::factory()->create();
        $admin = User::factory()->orgAdmin()->create(['organization_id' => $org->id]);
        $plan = Plan::factory()->for($org)->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $admin->id,
            'plan_id' => $plan->id,
        ]);

        $this->actingAs($admin)->get('/subscriptions')->assertOk();
        $this->actingAs($admin)->get('/subscriptions/'.$subscription->id)->assertOk();

        $this->actingAs($admin)->get('/subscriptions/create')->assertForbidden();
        $this->actingAs($admin)->post('/subscriptions', ['plan_id' => $plan->id])->assertForbidden();
        $this->actingAs($admin)->post('/payment/checkout/'.$plan->id)->assertForbidden();
        $this->actingAs($admin)->post('/subscriptions/'.$subscription->id.'/cancel')->assertForbidden();
    }

    public function test_student_can_open_the_subscribe_page(): void
    {
        $org = Organization::factory()->create();
        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        Plan::factory()->for($org)->create();

        $this->actingAs($student)->get('/subscriptions')->assertOk();
        $this->actingAs($student)->get('/subscriptions/create')->assertOk();
    }

    public function test_teachers_cannot_access_subscriptions(): void
    {
        $org = Organization::factory()->create();
        $teacher = User::factory()->teacher()->create(['organization_id' => $org->id]);

        $this->actingAs($teacher)->get('/subscriptions')->assertForbidden();
        $this->actingAs($teacher)->get('/subscriptions/create')->assertForbidden();
    }

    public function test_system_admin_manages_subscriptions_but_cannot_subscribe(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();
        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        $subscription = Subscription::factory()->create([
            'user_id' => $student->id,
            'plan_id' => $plan->id,
        ]);

        $systemAdmin = User::factory()->systemAdmin()->create();

        $this->actingAs($systemAdmin)
            ->get('/subscriptions')
            ->assertOk()
            ->assertViewHas('subscriptions', fn ($subscriptions) => $subscriptions->contains('id', $subscription->id));

        $this->actingAs($systemAdmin)->get('/subscriptions/'.$subscription->id)->assertOk();

        // A system admin oversees subscriptions but never subscribes himself.
        $this->actingAs($systemAdmin)->get('/subscriptions/create')->assertForbidden();
        $this->actingAs($systemAdmin)->post('/subscriptions', ['plan_id' => $plan->id])->assertForbidden();
        $this->actingAs($systemAdmin)->post('/payment/checkout/'.$plan->id)->assertForbidden();
        $this->actingAs($systemAdmin)->post('/subscriptions/'.$subscription->id.'/cancel')->assertForbidden();
    }

    public function test_system_admin_can_update_and_delete_a_subscription(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();
        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        $subscription = Subscription::factory()->create([
            'user_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'trialing',
        ]);

        $systemAdmin = User::factory()->systemAdmin()->create();

        $this->actingAs($systemAdmin)->get('/subscriptions/'.$subscription->id.'/edit')->assertOk();

        $this->actingAs($systemAdmin)
            ->patch('/subscriptions/'.$subscription->id, [
                'status' => 'active',
                'current_period_start' => '2026-01-01',
                'current_period_end' => '2026-02-01',
            ])
            ->assertRedirect('/subscriptions/'.$subscription->id);

        $subscription->refresh();
        $this->assertSame('active', $subscription->status->value);
        $this->assertSame('2026-02-01', $subscription->current_period_end->toDateString());

        $this->actingAs($systemAdmin)
            ->delete('/subscriptions/'.$subscription->id)
            ->assertRedirect('/subscriptions');

        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    }

    public function test_only_system_admin_can_update_or_delete_subscriptions(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();
        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        $admin = User::factory()->orgAdmin()->create(['organization_id' => $org->id]);

        $subscription = Subscription::factory()->create([
            'user_id' => $student->id,
            'plan_id' => $plan->id,
        ]);

        $payload = [
            'status' => 'active',
            'current_period_start' => '2026-01-01',
            'current_period_end' => '2026-02-01',
        ];

        foreach ([$student, $admin] as $user) {
            $this->actingAs($user)->get('/subscriptions/'.$subscription->id.'/edit')->assertForbidden();
            $this->actingAs($user)->patch('/subscriptions/'.$subscription->id, $payload)->assertForbidden();
            $this->actingAs($user)->delete('/subscriptions/'.$subscription->id)->assertForbidden();
        }

        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id]);
    }

    public function test_index_shows_organization_name_instead_of_id(): void
    {
        $org = Organization::factory()->create(['name' => 'Northwind Academy']);
        $plan = Plan::factory()->for($org)->create(['name' => 'Growth Plan']);
        $student = User::factory()->student()->create(['organization_id' => $org->id]);

        Subscription::factory()->create(['user_id' => $student->id, 'plan_id' => $plan->id]);

        $this->actingAs($student)
            ->get('/subscriptions')
            ->assertOk()
            ->assertSeeInOrder(['Plan', 'Organization', 'Status', 'Period end'])
            ->assertSee('Growth Plan')
            ->assertSee('Northwind Academy')
            ->assertDontSee('>ID<', false);
    }

    public function test_student_only_sees_their_own_subscriptions(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();

        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        $peer = User::factory()->student()->create(['organization_id' => $org->id]);
        $admin = User::factory()->orgAdmin()->create(['organization_id' => $org->id]);

        $mine = Subscription::factory()->create(['user_id' => $student->id, 'plan_id' => $plan->id]);
        Subscription::factory()->create(['user_id' => $peer->id, 'plan_id' => $plan->id]);
        Subscription::factory()->create(['user_id' => $admin->id, 'plan_id' => $plan->id]);

        $this->actingAs($student)
            ->get('/subscriptions')
            ->assertOk()
            ->assertViewHas('subscriptions', fn ($subscriptions) => $subscriptions->pluck('id')->all() === [$mine->id]);
    }

    public function test_org_admin_still_sees_every_subscription_in_the_organization(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();
        $admin = User::factory()->orgAdmin()->create(['organization_id' => $org->id]);

        Subscription::factory()->count(2)->create(['plan_id' => $plan->id]);
        Subscription::factory()->create(['user_id' => $admin->id, 'plan_id' => $plan->id]);

        $this->actingAs($admin)
            ->get('/subscriptions')
            ->assertOk()
            ->assertViewHas('subscriptions', fn ($subscriptions) => $subscriptions->count() === 3);
    }

    public function test_student_cannot_view_or_cancel_a_peers_subscription(): void
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();

        $student = User::factory()->student()->create(['organization_id' => $org->id]);
        $peer = User::factory()->student()->create(['organization_id' => $org->id]);

        $peerSubscription = Subscription::factory()->create([
            'user_id' => $peer->id,
            'plan_id' => $plan->id,
        ]);

        $this->actingAs($student)->get('/subscriptions/'.$peerSubscription->id)->assertNotFound();
        $this->actingAs($student)->post('/subscriptions/'.$peerSubscription->id.'/cancel')->assertNotFound();

        $this->assertNotSame('canceled', $peerSubscription->fresh()->status->value);
    }
}
