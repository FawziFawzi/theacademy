@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-pill text-sm font-medium bg-accent-soft text-accent border border-transparent transition-colors-fast'
            : 'inline-flex items-center px-3 py-2 rounded-pill text-sm font-medium text-ink-muted hover:text-ink hover:bg-surface-raised border border-transparent transition-colors-fast';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
