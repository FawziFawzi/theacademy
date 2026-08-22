<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Transactions</h2>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">ID</th>
                        <th class="text-left font-medium px-5 py-3">Type</th>
                        <th class="text-right font-medium px-5 py-3">Amount</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('transactions.show', $transaction) }}" class="font-medium text-ink hover:text-accent tabular-nums">#{{ $transaction->id }}</a></td>
                            <td class="px-5 py-3 text-ink-muted">{{ $transaction->type->label() }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium">${{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$transaction->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $transaction->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">No transactions found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
