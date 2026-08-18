<x-app-layout>
    <x-slot name="header">
        <h2>Plans</h2>
    </x-slot>

    <p><a href="{{ route('plans.create') }}">New plan</a></p>

    <ul>
        @foreach ($plans as $plan)
            <li>
                <a href="{{ route('plans.show', $plan) }}">{{ $plan->name }}</a>
                — {{ $plan->price }} / {{ $plan->billing_interval->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>