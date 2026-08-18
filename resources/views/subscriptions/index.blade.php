<x-app-layout>
    <x-slot name="header">
        <h2>Subscriptions</h2>
    </x-slot>

    <p><a href="{{ route('subscriptions.create') }}">New subscription</a></p>

    <ul>
        @foreach ($subscriptions as $subscription)
            <li>
                <a href="{{ route('subscriptions.show', $subscription) }}">#{{ $subscription->id }}</a>
                — {{ $subscription->plan?->name }} — {{ $subscription->status->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>