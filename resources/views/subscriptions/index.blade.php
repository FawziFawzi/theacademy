<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Subscriptions</h2>
            <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">New subscription</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">ID</th>
                        <th class="text-left font-medium px-5 py-3">Plan</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Period end</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($subscriptions as $subscription)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('subscriptions.show', $subscription) }}" class="font-medium text-ink hover:text-accent tabular-nums">#{{ $subscription->id }}</a></td>
                            <td class="px-5 py-3 text-ink-muted">{{ $subscription->plan?->name ?? '—' }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$subscription->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $subscription->current_period_end?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No subscriptions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
