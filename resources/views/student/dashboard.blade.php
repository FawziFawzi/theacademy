<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">My learning</h2>
        <p class="text-sm text-ink-muted mt-1">Your subscriptions and available plans</p>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-line flex items-center justify-between">
            <h3 class="section-heading">My subscriptions</h3>
            <a href="{{ route('subscriptions.create') }}" class="btn btn-primary text-sm !py-2">Browse plans</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Subscription</th>
                        <th class="text-left font-medium px-5 py-3">Plan</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Period</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('subscriptions.show', $sub) }}" class="font-medium text-ink hover:text-accent tabular-nums">#{{ $sub->id }}</a></td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-ink">{{ $sub->plan?->name }}</p>
                                <p class="text-xs text-ink-muted tabular-nums">${{ number_format($sub->plan?->price, 2) }} / {{ $sub->plan?->billing_interval?->label() }}</p>
                            </td>
                            <td class="px-5 py-3"><x-status-pill :status="$sub->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $sub->current_period_start?->format('Y-m-d') }} → {{ $sub->current_period_end?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No subscriptions yet — explore plans below</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-line">
            <h3 class="section-heading">Available plans</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Plan</th>
                        <th class="text-right font-medium px-5 py-3">Price</th>
                        <th class="text-left font-medium px-5 py-3">Billing</th>
                        <th class="text-right font-medium px-5 py-3">Courses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($availablePlans as $plan)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3 font-medium text-ink">{{ $plan->name }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium">${{ number_format($plan->price, 2) }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $plan->billing_interval->label() }}</td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted">{{ $plan->courses_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-ink-muted">No plans in your organization yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
