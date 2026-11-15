@props(['for' => null, 'label' => '', 'required' => false, 'hint' => null])

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6 items-start">
    @if($for)
        <label for="{{ $for }}" class="text-sm font-medium text-ink md:pt-2">{{ $label }}@if($required) <span class="text-accent">*</span>@endif</label>
    @else
        <span class="text-sm font-medium text-ink md:pt-2">{{ $label }}@if($required) <span class="text-accent">*</span>@endif</span>
    @endif
    <div class="md:col-span-2">
        {{ $slot }}
        @if($hint)
            <p class="text-xs text-ink-muted mt-1.5">{{ $hint }}</p>
        @endif
    </div>
</div>
