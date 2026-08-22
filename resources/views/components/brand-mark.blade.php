@props(['size' => 32])

{{-- Mountain-icon mark — nav + favicon. Accent peaks on surface. --}}
<svg
    {{ $attributes->merge(['class' => 'shrink-0']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 32 32"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <rect width="32" height="32" rx="8" fill="var(--color-accent-soft, #173B35)" />
    {{-- three peaks --}}
    <path d="M6 22L12.2 10.5L16.8 17.8L20.2 12.6L26 22H6Z" fill="var(--color-accent, #2FBFA8)" />
    <path d="M11.4 22L14.1 16.9L16.7 20.4L11.4 22Z" fill="#0E1614" opacity="0.28" />
    <path d="M19.6 22L21.8 17.2L23.9 22H19.6Z" fill="#0E1614" opacity="0.18" />
</svg>
