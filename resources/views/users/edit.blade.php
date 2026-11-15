<x-app-layout>
    <x-form-shell
        :backHref="route('users.show', $user)"
        backLabel="Back to User"
        :title="'Edit ' . $user->name"
        subtitle="Update account details or reset the member password."
    >
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <x-form-section
                    title="Account Details"
                    subtitle="Identity attributes used for sign-in and notifications."
                />

                <x-form-row for="name" label="Name" :required="true">
                    <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required placeholder="e.g. Jane Doe" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </x-form-row>

                <x-form-row for="email" label="Email" :required="true">
                    <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required placeholder="jane@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </x-form-row>
            </div>

            <div class="space-y-6 pt-2">
                <x-form-section
                    title="Credentials"
                    subtitle="Leave blank to keep the current password."
                />

                <x-form-row for="password" label="Password" hint="Leave blank to keep the current password.">
                    <x-text-input id="password" name="password" type="password" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </x-form-row>

                <x-form-row for="password_confirmation" label="Confirm Password">
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('users.show', $user)" submitLabel="Save changes" />
        </form>
    </x-form-shell>
</x-app-layout>
