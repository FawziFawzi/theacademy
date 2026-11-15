<x-form-section
    title="Profile Information"
    subtitle="Update your account's profile information and email address."
/>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')

    <x-form-row for="name" label="Name" :required="true">
        <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </x-form-row>

    <x-form-row for="email" label="Email" :required="true">
        <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-ink-muted">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-ink-muted hover:text-ink rounded-card focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-accent">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif
    </x-form-row>

    <div class="pt-2 flex items-center gap-4 sm:justify-end">
        <x-primary-button>{{ __('Save') }}</x-primary-button>

        @if (session('status') === 'profile-updated')
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
