<x-app-layout>
    <x-slot name="header">
        <h2>Transaction #{{ $transaction->id }}</h2>
    </x-slot>

    <p>Type: {{ $transaction->type->label() }}</p>
    <p>Amount: {{ $transaction->amount }}</p>
    <p>Status: {{ $transaction->status->label() }}</p>

    <p><a href="{{ route('transactions.index') }}">Back</a></p>
</x-app-layout>