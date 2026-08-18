<x-app-layout>
    <x-slot name="header">
        <h2>Subscription #{{ $subscription->id }}</h2>
    </x-slot>

    <p>Plan: {{ $subscription->plan?->name }}</p>
    <p>Status: {{ $subscription->status->label() }}</p>
    <p>Period: {{ $subscription->current_period_start?->toDateString() }} → {{ $subscription->current_period_end?->toDateString() }}</p>

    <p><a href="{{ route('subscriptions.index') }}">Back</a></p>
</x-app-layout>