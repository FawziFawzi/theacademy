<x-app-layout>
    <x-slot name="header">
        <h2>New subscription</h2>
    </x-slot>

    <form method="POST" action="{{ route('subscriptions.store') }}">
        @csrf

        <p>
            <label>Plan
                <select name="plan_id">
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} — {{ $plan->price }} / {{ $plan->billing_interval->label() }}</option>
                    @endforeach
                </select>
            </label>
        </p>
        <p><button type="submit">Subscribe</button></p>
    </form>

    <p><a href="{{ route('subscriptions.index') }}">Back</a></p>
</x-app-layout>