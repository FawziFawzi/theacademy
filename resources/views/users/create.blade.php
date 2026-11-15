<x-app-layout>
    <x-form-shell
        :backHref="route('users.index')"
        backLabel="Back to Users"
        title="New user"
        subtitle="Invite a member to your organization portal."
        badge="Ready to Invite"
    >
        <form method="POST" action="{{ route('users.store') }}" class="space-y-8">
            @csrf

            <div class="space-y-6">
                <x-form-section
                    title="Account Details"
                    subtitle="Identity attributes used for sign-in and notifications."
                />

                <x-form-row for="name" label="Name" :required="true">
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus placeholder="e.g. Jane Doe" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </x-form-row>

                <x-form-row for="email" label="Email" :required="true">
                    <x-text-input id="email" name="email" type="email" :value="old('email')" required placeholder="jane@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Credentials"
                    subtitle="Initial password the new member signs in with."
                />

                <x-form-row for="password" label="Password" :required="true">
                    <x-text-input id="password" name="password" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </x-form-row>

                <x-form-row for="password_confirmation" label="Confirm Password" :required="true">
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('users.index')" submitLabel="Create user" />
        </form>
    </x-form-shell>
</x-app-layout>
