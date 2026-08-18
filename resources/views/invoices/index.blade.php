<x-app-layout>
    <x-slot name="header">
        <h2>Invoices</h2>
    </x-slot>

    <ul>
        @foreach ($invoices as $invoice)
            <li>
                <a href="{{ route('invoices.show', $invoice) }}">#{{ $invoice->id }}</a>
                — {{ $invoice->status->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>