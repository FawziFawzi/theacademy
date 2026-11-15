@props(['backHref' => null, 'backLabel' => 'Back', 'title' => '', 'subtitle' => null, 'badge' => null])

<div class="max-w-4xl mx-auto">
    @if($backHref)
        <a href="{{ $backHref }}" class="inline-flex items-center gap-2 text-sm font-medium text-ink-muted hover:text-accent transition-colors-fast mb-3 group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            {{ $backLabel }}
        </a>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-2 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-ink tracking-tight font-display">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-sm text-ink-muted mt-1.5">{{ $subtitle }}</p>
            @endif
        </div>
        @if($badge)
            <div class="flex items-center gap-2 px-3 py-1 rounded-pill bg-surface-raised border border-line self-start sm:self-center">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse" aria-hidden="true"></span>
                <span class="text-xs text-ink-muted font-medium tracking-wide">{{ $badge }}</span>
            </div>
        @endif
    </div>

    <section class="form-card bg-surface border border-line rounded-card shadow-card p-6 sm:p-10 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-60 h-60 bg-accent/5 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
        {{ $slot }}
    </section>
</div>
