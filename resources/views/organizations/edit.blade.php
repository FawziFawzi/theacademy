<x-app-layout>
    <x-form-shell
        :backHref="route('organizations.show', $organization)"
        backLabel="Back to Organization"
        :title="'Edit ' . $organization->name"
        subtitle="Update the tenant's core details and contact information."
    >
        <form method="POST" action="{{ route('organizations.update', $organization) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <x-form-section
                    title="Core Organization Details"
                    subtitle="Primary identification attributes visible across administrative dashboards."
                />

                <x-form-row for="name" label="Organization Name" :required="true" hint="Official registered entity or academic department name.">
                    <x-text-input id="name" name="name" type="text" :value="old('name', $organization->name)" required placeholder="e.g. Apex Institute of Technology" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </x-form-row>

                <x-form-row for="address" label="Campus / Mailing Address" hint="Primary headquarters or campus physical address for invoicing and certificates.">
                    <textarea id="address" name="address" rows="3" placeholder="452 University Ave, Suite 300" class="field-input resize-y">{{ old('address', $organization->address) }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Primary Contact Information"
                    subtitle="Contact credentials for system notices and administrative communication."
                />

                <x-form-row for="email" label="Admin Email" :required="true">
                    <x-text-input id="email" name="email" type="email" :value="old('email', $organization->email)" required placeholder="administrator@apextech.edu" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </x-form-row>

                <x-form-row for="phone" label="Phone Number">
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $organization->phone)" placeholder="+1 (555) 234-5678" class="tabular-nums" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('organizations.show', $organization)" submitLabel="Save changes" />
        </form>
    </x-form-shell>
</x-app-layout>
