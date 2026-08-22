<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">{{ $organization?->name ?? 'My organization' }}</h2>
        <p class="text-sm text-ink-muted mt-1 flex items-center gap-2">
            @if($organization) <x-status-pill :status="$organization->status" /> <span class="text-ink-muted">·</span> {{ $organization->email }} @endif
        </p>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card p-5">
            <p class="label">Users</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['users']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Courses</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['courses']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Plans</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['plans']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Subscriptions</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['subscriptions']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Revenue</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">${{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-line flex items-center justify-between">
            <h3 class="section-heading">Courses</h3>
            <a href="{{ route('courses.index') }}" class="text-sm font-medium underline decoration-line underline-offset-4">Manage</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Course</th>
                        <th class="text-left font-medium px-5 py-3">Teacher</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($courses as $course)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('courses.show', $course) }}" class="font-medium text-ink hover:text-accent">{{ $course->title }}</a></td>
                            <td class="px-5 py-3 text-ink-muted">{{ $course->teacher?->name }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$course->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $course->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-ink-muted">No courses yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mt-6">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-line flex items-center justify-between">
                <h3 class="section-heading">Recent subscriptions</h3>
                <a href="{{ route('subscriptions.index') }}" class="text-sm font-medium underline decoration-line underline-offset-4">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-ink-muted text-xs uppercase tracking-wide">
                        <tr class="border-b border-line">
                            <th class="text-left font-medium px-5 py-3">Student</th>
                            <th class="text-left font-medium px-5 py-3">Plan</th>
                            <th class="text-left font-medium px-5 py-3">Status</th>
                            <th class="text-right font-medium px-5 py-3">Ends</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($subscriptions as $sub)
                            <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                                <td class="px-5 py-3"><p class="font-medium text-ink tabular-nums">#{{ $sub->id }}</p><p class="text-xs text-ink-muted">{{ $sub->user?->name }}</p></td>
                                <td class="px-5 py-3 text-ink-muted">{{ $sub->plan?->name }}</td>
                                <td class="px-5 py-3"><x-status-pill :status="$sub->status" /></td>
                                <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $sub->current_period_end?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-ink-muted">No subscriptions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-line">
                <h3 class="section-heading">Recent transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-ink-muted text-xs uppercase tracking-wide">
                        <tr class="border-b border-line">
                            <th class="text-left font-medium px-5 py-3">Tx</th>
                            <th class="text-left font-medium px-5 py-3">Type</th>
                            <th class="text-right font-medium px-5 py-3">Amount</th>
                            <th class="text-left font-medium px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                                <td class="px-5 py-3 tabular-nums"><a href="{{ route('transactions.show', $tx) }}" class="font-medium text-ink hover:text-accent">#{{ $tx->id }}</a></td>
                                <td class="px-5 py-3 text-ink-muted">{{ $tx->type->label() }}</td>
                                <td class="px-5 py-3 text-right tabular-nums font-medium">${{ number_format($tx->amount, 2) }}</td>
                                <td class="px-5 py-3"><x-status-pill :status="$tx->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-ink-muted">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
