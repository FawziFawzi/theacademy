<x-app-layout>
    <x-slot name="header">
        <h2>Invoice #{{ $invoice->id }}</h2>
    </x-slot>

    <p>Status: {{ $invoice->status->label() }}</p>
    <p>Transaction: #{{ $invoice->transaction_id }}</p>

    <p><a href="{{ route('invoices.index') }}">Back</a></p>
</x-app-layout>