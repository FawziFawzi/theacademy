<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Plans</h2>
            <a href="{{ route('plans.create') }}" class="btn btn-primary">New plan</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Plan</th>
                        <th class="text-right font-medium px-5 py-3">Price</th>
                        <th class="text-left font-medium px-5 py-3">Billing</th>
                        <th class="text-right font-medium px-5 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($plans as $plan)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('plans.show', $plan) }}" class="font-medium text-ink hover:text-accent">{{ $plan->name }}</a></td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium">${{ number_format($plan->price, 2) }}</td>
                            <td class="px-5 py-3"><span class="status-pill pill-muted">{{ $plan->billing_interval->label() }}</span></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $plan->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No plans found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
