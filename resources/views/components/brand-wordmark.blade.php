@props(['onDark' => false])

{{-- Wordmark: green "Th" + neutral "eAcademy". Never recolor the green.
     On dark backgrounds pass :on-dark="true" so the neutral text uses --color-text. --}}
@php
    $neutral = $onDark ? 'var(--color-text, #EAF2F0)' : '#1a2a27';
    // On dark the muted tagline also flips to muted-on-dark.
    $taglineColor = $onDark ? 'var(--color-text-muted, #8FA39E)' : '#6b7d79';
@endphp
<div {{ $attributes->merge(['class' => 'inline-flex flex-col items-start leading-none select-none']) }} aria-label="The Academy">
    <div class="flex items-baseline gap-0 font-display font-bold tracking-tight" style="font-family: var(--font-display, 'Manrope', sans-serif);">
        <span style="color: var(--color-accent, #2FBFA8); font-size: 1.9rem; letter-spacing: -0.04em;">Th</span><span style="color: {{ $neutral }}; font-size: 1.9rem; letter-spacing: -0.04em;">eAcademy</span>
    </div>
    <span class="tracking-[0.22em] uppercase font-medium" style="color: {{ $taglineColor }}; font-size: 0.62rem; margin-top: 2px; font-family: var(--font-body, 'Inter', sans-serif);">Learn The Way</span>
</div>
