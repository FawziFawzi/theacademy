<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Dashboard</h2>
    </x-slot>

    <div class="card p-6">
        <p class="text-ink">{{ __("You're logged in!") }}</p>
        <p class="text-sm text-ink-muted mt-2">Use the navigation above to explore your workspace.</p>
    </div>
</x-app-layout>
