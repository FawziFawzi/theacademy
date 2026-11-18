<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'The Academy') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        {{-- Manrope (display) + Inter (body) — bunny.net --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen flex flex-col bg-canvas text-ink antialiased font-sans">
        {{-- Top navigation --}}
        <header class="bg-surface border-b border-line sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16" x-data="{ open: false }">
                    {{-- Left: mark + primary nav --}}
                    <div class="flex items-center gap-8">
                        <a href="{{ route('dashboard') }}" class="flex items-center shrink-0 rounded-card focus-visible:outline-none" aria-label="The Academy — home">
                            <x-brand-mark :size="32" />
                        </a>

                        @auth
                        <nav class="hidden sm:flex items-center gap-1" aria-label="Primary">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>

                            @if(in_array(auth()->user()->role?->value, ['system_admin']))
                                <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                                    {{ __('Organizations') }}
                                </x-nav-link>
                            @endif

                            @if(in_array(auth()->user()->role?->value, ['system_admin','org_admin','teacher']))
                                <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                                    {{ __('Courses') }}
                                </x-nav-link>
                            @endif

                            @if(in_array(auth()->user()->role?->value, ['system_admin','org_admin']))
                                <x-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.*')">
                                    {{ __('Plans') }}
                                </x-nav-link>
                                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    {{ __('Users') }}
                                </x-nav-link>
                            @endif

                            @if(in_array(auth()->user()->role?->value, ['org_admin','student','system_admin']))
                                <x-nav-link :href="route('subscriptions.index')" :active="request()->routeIs('subscriptions.*')">
                                    {{ __('Subscriptions') }}
                                </x-nav-link>
                            @endif
                        </nav>
                        @endauth
                    </div>

                    {{-- Right: org badge + avatar + dropdown --}}
                    <div class="flex items-center gap-3">
                        @auth
                        @php
                            $u = auth()->user();
                            $initials = collect(explode(' ', trim($u->name ?? '')))->filter()->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
                            if ($initials === '') $initials = mb_strtoupper(mb_substr($u->email ?? 'U', 0, 1));
                            $orgName = $u->relationLoaded('organization') ? $u->organization?->name : $u->organization()->pluck('name')->first();
                            $showOrg = ($u->role?->value ?? null) !== 'system_admin' && ! empty($orgName);
                        @endphp
                        @if($showOrg)
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-card bg-surface-raised border border-line text-xs text-ink" aria-label="Current organization">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-accent animate-pulse shrink-0" aria-hidden="true"></span>
                                <svg class="w-3.5 h-3.5 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span class="font-medium text-ink max-w-[130px] sm:max-w-[200px] truncate" title="{{ $orgName }}">{{ $orgName }}</span>
                            </div>
                            <span class="hidden sm:inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold bg-accent-soft text-accent border border-accent/20">Tenant</span>
                        </div>
                        @endif
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-3 rounded-pill pl-1 pr-3 py-1 bg-surface-raised border border-line hover:border-accent/40 transition-colors-fast focus-visible:outline-none">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-accent-soft text-accent text-xs font-semibold shrink-0" aria-hidden="true">{{ $initials }}</span>
                                        <span class="text-sm font-medium text-ink max-w-[14ch] truncate">{{ $u->name }}</span>
                                        <svg class="w-4 h-4 text-ink-muted shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="px-4 py-3 border-b border-line">
                                        <p class="text-sm font-medium text-ink truncate">{{ $u->name }}</p>
                                        <p class="text-xs text-ink-muted truncate">{{ $u->email }}</p>
                                    </div>
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        {{-- Mobile hamburger --}}
                        <div class="flex items-center sm:hidden">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-card text-ink-muted hover:text-ink hover:bg-surface-raised border border-transparent hover:border-line transition-colors-fast" aria-label="Toggle navigation">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        @endauth
                    </div>
                </div>

                {{-- Mobile nav panel --}}
                @auth
                <div x-show="open" x-transition class="sm:hidden border-t border-line -mx-4 px-2 py-3 space-y-1" style="display: none;">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
                    @if(in_array(auth()->user()->role?->value, ['system_admin']))
                        <x-responsive-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">{{ __('Organizations') }}</x-responsive-nav-link>
                    @endif
                    @if(in_array(auth()->user()->role?->value, ['system_admin','org_admin','teacher']))
                        <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">{{ __('Courses') }}</x-responsive-nav-link>
                    @endif
                    @if(in_array(auth()->user()->role?->value, ['system_admin','org_admin']))
                        <x-responsive-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.*')">{{ __('Plans') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">{{ __('Users') }}</x-responsive-nav-link>
                    @endif
                    @if(in_array(auth()->user()->role?->value, ['org_admin','student','system_admin']))
                        <x-responsive-nav-link :href="route('subscriptions.index')" :active="request()->routeIs('subscriptions.*')">{{ __('Subscriptions') }}</x-responsive-nav-link>
                    @endif
                    <div class="pt-3 mt-3 border-t border-line space-y-1">
                        <div class="px-3 py-2">
                            <p class="text-sm font-medium text-ink">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-ink-muted truncate">{{ auth()->user()->email }}</p>
                            @if(($u->role?->value ?? null) !== 'system_admin' && ! empty($orgName))
                            <p class="mt-1 inline-flex items-center gap-1.5 text-xs text-ink-muted">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent" aria-hidden="true"></span>
                                <span class="truncate">{{ $orgName }}</span>
                            </p>
                            @endif
                        </div>
                        <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        {{-- Page heading (optional) --}}
        @isset($header)
            <div class="bg-surface border-b border-line">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    <div class="font-display font-semibold text-ink [&_h2]:page-title [&_h2]:text-ink">
                        {{ $header }}
                    </div>
                </div>
            </div>
        @endisset

        {{-- Page content --}}
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-surface border-t border-line mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-sm text-ink-muted">© {{ date('Y') }} The Academy · <span class="font-medium tracking-wide">Learn The Way</span></p>
                <p class="text-xs text-ink-muted/80">Crafted for institutes, teachers & students</p>
            </div>
        </footer>
    </body>
</html>
