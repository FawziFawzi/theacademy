<x-app-layout>
    <x-form-shell
        :backHref="route('organizations.index')"
        backLabel="Back to Organizations"
        title="New organization"
        subtitle="Register a tenant instance to provide isolated LMS portals, courses, and localized billing."
        badge="Ready to Provision"
    >
        <form method="POST" action="{{ route('organizations.store') }}" class="space-y-8">
            @csrf

            <div class="space-y-6">
                <x-form-section
                    title="Core Organization Details"
                    subtitle="Primary identification attributes visible across administrative dashboards."
                />

                <x-form-row for="name" label="Organization Name" :required="true" hint="Official registered entity or academic department name.">
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required placeholder="e.g. Apex Institute of Technology" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </x-form-row>

                <x-form-row for="address" label="Campus / Mailing Address" hint="Primary headquarters or campus physical address for invoicing and certificates.">
                    <textarea id="address" name="address" rows="3" placeholder="452 University Ave, Suite 300" class="field-input resize-y">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Primary Contact Information"
                    subtitle="Contact credentials for system notices and administrative communication."
                />

                <x-form-row for="email" label="Admin Email" :required="true">
                    <x-text-input id="email" name="email" type="email" :value="old('email')" required placeholder="administrator@apextech.edu" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </x-form-row>

                <x-form-row for="phone" label="Phone Number">
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone')" placeholder="+1 (555) 234-5678" class="tabular-nums" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('organizations.index')" submitLabel="Create organization" />
        </form>
    </x-form-shell>
</x-app-layout>
