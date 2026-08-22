<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Invoices</h2>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">ID</th>
                        <th class="text-left font-medium px-5 py-3">Transaction</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-ink hover:text-accent tabular-nums">#{{ $invoice->id }}</a></td>
                            <td class="px-5 py-3 tabular-nums text-ink-muted">#{{ $invoice->transaction_id }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$invoice->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $invoice->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No invoices found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
