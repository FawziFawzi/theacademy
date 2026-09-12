<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">New subscription</h2>
    </x-slot>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="section-heading mb-4">Direct subscribe</h3>
            <p class="text-sm text-ink-muted mb-4">Creates subscription + transaction immediately (non-gateway, for testing).</p>
            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf
                <p>
                    <label class="label">Plan
                        <select name="plan_id" class="mt-2 w-full rounded-card border border-line bg-surface-raised px-3 py-2">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} — ${{ number_format($plan->price, 2) }} / {{ $plan->billing_interval->label() }}</option>
                            @endforeach
                        </select>
                    </label>
                </p>
                <p class="mt-4"><button type="submit" class="btn btn-tertiary w-full">Subscribe directly</button></p>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="section-heading mb-4">Pay with Stripe</h3>
            <p class="text-sm text-ink-muted mb-4">Redirects to Stripe Checkout; webhook confirms payment.</p>
            <form id="payment-checkout-form" method="POST" onsubmit="event.preventDefault(); const id=document.getElementById('payment_plan_id').value; this.action='{{ url('payment/checkout') }}/'+id; this.submit();">
                @csrf
                <p>
                    <label class="label">Plan
                        <select id="payment_plan_id" name="plan_id" class="mt-2 w-full rounded-card border border-line bg-surface-raised px-3 py-2">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} — ${{ number_format($plan->price, 2) }} / {{ $plan->billing_interval->label() }}</option>
                            @endforeach
                        </select>
                    </label>
                </p>
                <p class="mt-4"><button type="submit" class="btn btn-primary w-full">Checkout with Stripe</button></p>
            </form>
        </div>
    </div>

    <p class="mt-6"><a href="{{ route('subscriptions.index') }}" class="btn btn-tertiary">Back</a></p>
</x-app-layout>
