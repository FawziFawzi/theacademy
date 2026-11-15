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
                <x-form-section
                    title="Direct Subscribe"
                    subtitle="Creates subscription + transaction immediately (non-gateway, for testing)."
                />

                <form method="POST" action="{{ route('subscriptions.store') }}" class="space-y-6">
                    @csrf
                    <x-form-row for="plan_id" label="Plan" :required="true">
                        <select id="plan_id" name="plan_id" required class="field-input">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} — ${{ number_format($plan->price, 2) }} / {{ $plan->billing_interval->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('plan_id')" class="mt-2" />
                    </x-form-row>
                    <div class="pt-2 flex sm:justify-end">
                        <button type="submit" class="btn btn-secondary w-full sm:w-auto">Subscribe directly</button>
                    </div>
                </form>
            </div>

            <div class="space-y-6 pt-2 border-t border-line">
                <div class="pt-6">
                    <x-form-section
                        title="Pay with Stripe"
                        subtitle="Redirects to Stripe Checkout; webhook confirms payment."
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
