<x-app-layout>
    <x-slot name="header">
        <h2>Transactions</h2>
    </x-slot>

    <ul>
        @foreach ($transactions as $transaction)
            <li>
                <a href="{{ route('transactions.show', $transaction) }}">#{{ $transaction->id }}</a>
                — {{ $transaction->type->label() }} — {{ $transaction->amount }} — {{ $transaction->status->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>