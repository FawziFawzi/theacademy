<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
            <input id="remember_me" type="checkbox" class="rounded border-line bg-surface text-accent focus:ring-accent focus:ring-offset-0" name="remember">
            <span class="text-sm text-ink-muted">{{ __('Remember me') }}</span>
        </label>

        <div class="flex items-center justify-between gap-4 pt-2">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-ink-muted hover:text-accent underline underline-offset-4 decoration-line hover:decoration-accent transition-colors-fast">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
