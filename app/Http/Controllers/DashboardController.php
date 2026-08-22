<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::SystemAdmin => $this->systemAdmin(),
            UserRole::OrgAdmin => $this->orgAdmin($user),
            UserRole::Teacher => $this->teacher($user),
            UserRole::Student => $this->student($user),
            default => view('dashboard'),
        };
    }

    private function systemAdmin(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'organizations' => Organization::count(),
                'users' => User::count(),
                'courses' => Course::count(),
                'plans' => Plan::count(),
                'subscriptions' => Subscription::count(),
                'revenue' => Transaction::where('status', 'completed')->sum('amount'),
            ],
            'recentOrganizations' => Organization::latest()->take(5)->get(),
            'recentSubscriptions' => Subscription::with(['user', 'plan'])->latest()->take(5)->get(),
            'recentTransactions' => Transaction::with('subscription.plan')->latest()->take(5)->get(),
        ]);
    }

    private function orgAdmin(User $user): View
    {
        $orgId = $user->organization_id;

        return view('org-admin.dashboard', [
            'organization' => $user->organization,
            'stats' => [
                'users' => User::where('organization_id', $orgId)->count(),
                'courses' => Course::where('organization_id', $orgId)->count(),
                'plans' => Plan::where('organization_id', $orgId)->count(),
                'subscriptions' => Subscription::whereHas('plan', fn ($q) => $q->where('organization_id', $orgId))->count(),
                'revenue' => Transaction::whereHas('subscription.plan', fn ($q) => $q->where('organization_id', $orgId))
                    ->where('status', 'completed')->sum('amount'),
            ],
            'courses' => Course::where('organization_id', $orgId)->with('teacher')->latest()->take(5)->get(),
            'subscriptions' => Subscription::whereHas('plan', fn ($q) => $q->where('organization_id', $orgId))
                ->with(['user', 'plan'])->latest()->take(5)->get(),
            'transactions' => Transaction::whereHas('subscription.plan', fn ($q) => $q->where('organization_id', $orgId))
                ->latest()->take(5)->get(),
        ]);
    }

    private function teacher(User $user): View
    {
        $courses = Course::where('teacher_id', $user->id)->withCount('plans')->latest()->get();

        return view('teacher.dashboard', [
            'courses' => $courses,
            'stats' => [
                'courses' => $courses->count(),
                'published' => $courses->filter(fn (Course $c) => $c->status === CourseStatus::Published)->count(),
                'draft' => $courses->filter(fn (Course $c) => $c->status === CourseStatus::Draft)->count(),
            ],
        ]);
    }

    private function student(User $user): View
    {
        $subscriptions = Subscription::where('user_id', $user->id)->with(['plan.courses', 'transactions.invoice'])->latest()->get();
        $availablePlans = Plan::where('organization_id', $user->organization_id)->withCount('courses')->get();

        return view('student.dashboard', [
            'subscriptions' => $subscriptions,
            'availablePlans' => $availablePlans,
        ]);
    }
}
