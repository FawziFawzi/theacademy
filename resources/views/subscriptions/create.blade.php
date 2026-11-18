<x-app-layout>
    <x-form-shell
        :backHref="route('subscriptions.index')"
        backLabel="Back to Subscriptions"
        title="New subscription"
        subtitle="Subscribe to a plan — directly for testing, or via Stripe Checkout."
        badge="Ready to Subscribe"
    >
        <div class="space-y-8">

            <div class="space-y-6">
                <div class="pt-6">
                    <x-form-section
                        title="Pay with Stripe"
                        subtitle="Pay using Stripe and enjoy the plan"
                    />
                </div>

                <form id="payment-checkout-form" method="POST" onsubmit="event.preventDefault(); const id=document.getElementById('payment_plan_id').value; this.action='{{ url('payment/checkout') }}/'+id; this.submit();" class="space-y-6">
                    @csrf
                    <x-form-row for="payment_plan_id" label="Plan" :required="true">
                        <select id="payment_plan_id" name="plan_id" required class="field-input">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} — ${{ number_format($plan->price, 2) }} / {{ $plan->billing_interval->label() }}</option>
                            @endforeach
                        </select>
                    </x-form-row>
                    <div class="pt-2 flex sm:justify-end">
                        <button type="submit" class="btn btn-primary w-full sm:w-auto">Checkout with Stripe</button>
                    </div>
                </form>
            </div>
        </div>
    </x-form-shell>
</x-app-layout>
