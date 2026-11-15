<x-form-section
    title="Update Password"
    subtitle="Ensure your account is using a long, random password to stay secure."
/>

<form method="post" action="{{ route('password.update') }}" class="space-y-6">
    @csrf
    @method('put')

    <x-form-row for="update_password_current_password" label="Current Password" :required="true">
        <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
    </x-form-row>

    <x-form-row for="update_password_password" label="New Password" :required="true">
        <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
    </x-form-row>

    <x-form-row for="update_password_password_confirmation" label="Confirm Password" :required="true">
        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
    </x-form-row>

    <div class="pt-2 flex items-center gap-4 sm:justify-end">
        <x-primary-button>{{ __('Save') }}</x-primary-button>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-ink-muted"
            >{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
