@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-accent text-start text-sm font-medium text-accent bg-accent-soft focus:outline-none transition-colors-fast'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-sm font-medium text-ink-muted hover:text-ink hover:bg-surface-raised hover:border-line focus:outline-none transition-colors-fast';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
