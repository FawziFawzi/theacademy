<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Platform overview</h2>
        <p class="text-sm text-ink-muted mt-1">System-wide metrics — all organizations</p>
    </x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="card p-5">
            <p class="label">Organizations</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['organizations']) }}</p>
        </div>
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

    <div class="grid lg:grid-cols-2 gap-6 mt-6">
        {{-- Recent organizations --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-line flex items-center justify-between">
                <h3 class="section-heading">Recent organizations</h3>
                <a href="{{ route('organizations.index') }}" class="text-sm font-medium underline decoration-line underline-offset-4">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-ink-muted text-xs uppercase tracking-wide">
                        <tr class="border-b border-line">
                            <th class="text-left font-medium px-5 py-3">Organization</th>
                            <th class="text-left font-medium px-5 py-3">Status</th>
                            <th class="text-right font-medium px-5 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($recentOrganizations as $org)
                            <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                                <td class="px-5 py-3">
                                    <a href="{{ route('organizations.show', $org) }}" class="font-medium text-ink hover:text-accent">{{ $org->name }}</a>
                                    <p class="text-xs text-ink-muted">{{ $org->email }}</p>
                                </td>
                                <td class="px-5 py-3"><x-status-pill :status="$org->status" /></td>
                                <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $org->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-ink-muted">No organizations yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent subscriptions --}}
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
                            <th class="text-right font-medium px-5 py-3">Period</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($recentSubscriptions as $sub)
                            <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                                <td class="px-5 py-3">
                                    <a href="{{ route('subscriptions.show', $sub) }}" class="font-medium text-ink hover:text-accent">#{{ $sub->id }}</a>
                                    <p class="text-xs text-ink-muted">{{ $sub->user?->name }}</p>
                                </td>
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
    </div>

    <div class="card overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-line">
            <h3 class="section-heading">Recent transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Transaction</th>
                        <th class="text-left font-medium px-5 py-3">Type</th>
                        <th class="text-right font-medium px-5 py-3">Amount</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('transactions.show', $tx) }}" class="font-medium text-ink hover:text-accent tabular-nums">#{{ $tx->id }}</a></td>
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
</x-app-layout>
