<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Transaction #<span class="tabular-nums">{{ $transaction->id }}</span></h2>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="label">Type</dt>
                <dd class="mt-1 text-ink">{{ $transaction->type->label() }}</dd>
            </div>
            <div>
                <dt class="label">Status</dt>
                <dd class="mt-1"><x-status-pill :status="$transaction->status" /></dd>
            </div>
            <div>
                <dt class="label">Amount</dt>
                <dd class="mt-1 text-xl font-display font-bold tabular-nums">${{ number_format($transaction->amount, 2) }}</dd>
            </div>
            <div>
                <dt class="label">Date</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $transaction->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="label">Subscription</dt>
                <dd class="mt-1 tabular-nums"><a href="{{ route('subscriptions.show', $transaction->subscription) }}" class="hover:text-accent">#{{ $transaction->subscription_id }}</a></dd>
            </div>
            @if($transaction->invoice)
                <div>
                    <dt class="label">Invoice</dt>
                    <dd class="mt-1 tabular-nums"><a href="{{ route('invoices.show', $transaction->invoice) }}" class="hover:text-accent">#{{ $transaction->invoice->id }}</a> · <x-status-pill :status="$transaction->invoice->status" /></dd>
                </div>
            @endif
        </dl>
        <div class="mt-6">
            <a href="{{ route('transactions.index') }}" class="btn btn-tertiary">Back</a>
        </div>
    </div>
</x-app-layout>
