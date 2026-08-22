<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'The Academy') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen flex flex-col items-center justify-center bg-canvas text-ink antialiased font-sans px-4 py-12">
        {{-- Wordmark — centered on empty canvas (dark). Uses on-dark variant so "eAcademy" stays legible. --}}
        <div class="mb-10 flex flex-col items-center">
            <a href="/" class="focus-visible:outline-none rounded-card">
                <x-brand-wordmark :on-dark="true" />
            </a>
        </div>

        {{-- Auth card-less slot: form floats directly on canvas; inputs bring their own surface. --}}
        <div class="w-full sm:max-w-[420px]">
            {{ $slot }}
        </div>

        <p class="mt-10 text-xs text-ink-muted text-center">© {{ date('Y') }} The Academy · Learn The Way</p>
    </body>
</html>
