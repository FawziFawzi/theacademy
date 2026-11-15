@props(['title' => '', 'subtitle' => null])

<div class="border-b border-line pb-3">
    <h2 class="text-base font-semibold text-ink font-display">{{ $title }}</h2>
    @if($subtitle)
        <p class="text-xs text-ink-muted mt-0.5">{{ $subtitle }}</p>
    @endif
</div>
