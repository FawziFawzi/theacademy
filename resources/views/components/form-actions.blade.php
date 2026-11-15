@props(['backHref' => null, 'backLabel' => 'Back', 'submitLabel' => 'Save'])

<div class="pt-6 border-t border-line flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
    @if($backHref)
        <a href="{{ $backHref }}" class="btn border border-line text-ink-muted hover:text-ink hover:bg-canvas transition-colors-fast">{{ $backLabel }}</a>
    @endif
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
</div>
