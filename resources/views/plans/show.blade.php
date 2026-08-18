<x-app-layout>
    <x-slot name="header">
        <h2>{{ $plan->name }}</h2>
    </x-slot>

    <p>Price: {{ $plan->price }} / {{ $plan->billing_interval->label() }}</p>
    <p>Courses:</p>
    <ul>
        @foreach ($plan->courses as $course)
            <li>{{ $course->title }}</li>
        @endforeach
    </ul>

    <p><a href="{{ route('plans.edit', $plan) }}">Edit</a></p>
    <p><a href="{{ route('plans.index') }}">Back</a></p>
</x-app-layout>