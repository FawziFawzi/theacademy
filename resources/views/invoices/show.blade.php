<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Invoice #<span class="tabular-nums">{{ $invoice->id }}</span></h2>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="label">Status</dt>
                <dd class="mt-1"><x-status-pill :status="$invoice->status" /></dd>
            </div>
            <div>
                <dt class="label">Transaction</dt>
                <dd class="mt-1 tabular-nums"><a href="{{ route('transactions.show', $invoice->transaction) }}" class="hover:text-accent">#{{ $invoice->transaction_id }}</a></dd>
            </div>
            <div>
                <dt class="label">Amount</dt>
                <dd class="mt-1 tabular-nums font-medium">${{ number_format($invoice->transaction?->amount ?? 0, 2) }}</dd>
            </div>
            <div>
                <dt class="label">Created</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $invoice->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
            @if($invoice->pdf_path)
                <div class="sm:col-span-2">
                    <dt class="label">PDF</dt>
                    <dd class="mt-1 text-ink-muted tabular-nums">{{ $invoice->pdf_path }}</dd>
                </div>
            @endif
        </dl>
        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('invoices.index') }}" class="btn btn-tertiary">Back</a>
            @if($invoice->pdf_path)
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-primary">Download PDF</a>
            @endif
        </div>
    </div>
</x-app-layout>
