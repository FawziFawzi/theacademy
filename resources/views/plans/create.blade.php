<x-app-layout>
    <x-form-shell
        :backHref="route('plans.index')"
        backLabel="Back to Plans"
        title="New plan"
        subtitle="Bundle courses into a priced tier students can subscribe to."
        badge="Ready to Publish"
    >
        <form method="POST" action="{{ route('plans.store') }}" class="space-y-8">
            @csrf

            <div class="space-y-6">
                <x-form-section
                    title="Plan Details"
                    subtitle="Pricing and billing attributes shown at checkout."
                />

                <x-form-row for="name" label="Plan Name" :required="true" hint="Display name learners see when subscribing.">
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required placeholder="e.g. Growth Academy Plan" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </x-form-row>

                <x-form-row for="price" label="Price" :required="true" hint="Charge per billing period, in USD.">
                    <x-text-input id="price" name="price" type="number" step="0.01" min="0" :value="old('price')" required placeholder="49.00" class="tabular-nums" />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </x-form-row>

                <x-form-row for="billing_interval" label="Billing Interval" :required="true">
                    <select id="billing_interval" name="billing_interval" required class="field-input">
                        <option value="monthly" @selected(old('billing_interval') === 'monthly')>Monthly</option>
                        <option value="yearly" @selected(old('billing_interval') === 'yearly')>Yearly</option>
                    </select>
                    <x-input-error :messages="$errors->get('billing_interval')" class="mt-2" />
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Included Courses"
                    subtitle="Courses unlocked by an active subscription to this plan."
                />

                <x-form-row label="Courses" hint="Select at least one course for this plan.">
                    <div class="space-y-2">
                        @foreach ($courses as $course)
                            <label class="flex items-center gap-3 p-3 rounded-card bg-canvas border border-line cursor-pointer hover:border-ink-muted transition-colors-fast">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" @checked(in_array($course->id, old('course_ids', []), true)) class="rounded border-line bg-surface text-accent focus:ring-accent focus:ring-offset-0" />
                                <span class="text-sm text-ink">{{ $course->title }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('course_ids')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('plans.index')" submitLabel="Create plan" />
        </form>
    </x-form-shell>
</x-app-layout>
