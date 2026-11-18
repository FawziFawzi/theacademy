<x-app-layout>
    <x-form-shell
        :backHref="route('subscriptions.show', $subscription)"
        backLabel="Back to Subscription"
        title="Edit Subscription"
        subtitle="Adjust the billing status and current period for this subscription."
    >
        <form method="POST" action="{{ route('subscriptions.update', $subscription) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <x-form-section
                    title="Subscription"
                    subtitle="Plan and student this subscription belongs to."
                />

                <x-form-row label="Plan">
                    <p class="text-sm text-ink md:pt-2">{{ $subscription->plan?->name ?? '—' }}</p>
                </x-form-row>

                <x-form-row label="Organization">
                    <p class="text-sm text-ink md:pt-2">{{ $subscription->plan?->organization?->name ?? '—' }}</p>
                </x-form-row>

                <x-form-row label="Student">
                    <p class="text-sm text-ink md:pt-2">{{ $subscription->user?->name ?? '—' }}</p>
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Billing"
                    subtitle="Status and billing period of the subscription."
                />

                <x-form-row for="status" label="Status" :required="true">
                    <select id="status" name="status" required class="field-input">
                        @foreach (\App\Enums\SubscriptionStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $subscription->status?->value) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </x-form-row>

                <x-form-row for="current_period_start" label="Period Start" :required="true">
                    <x-text-input id="current_period_start" name="current_period_start" type="date" :value="old('current_period_start', $subscription->current_period_start?->format('Y-m-d'))" required class="tabular-nums" />
                    <x-input-error :messages="$errors->get('current_period_start')" class="mt-2" />
                </x-form-row>

                <x-form-row for="current_period_end" label="Period End" :required="true" hint="Must be on or after the period start.">
                    <x-text-input id="current_period_end" name="current_period_end" type="date" :value="old('current_period_end', $subscription->current_period_end?->format('Y-m-d'))" required class="tabular-nums" />
                    <x-input-error :messages="$errors->get('current_period_end')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('subscriptions.show', $subscription)" submitLabel="Save changes" />
        </form>
    </x-form-shell>
</x-app-layout>
