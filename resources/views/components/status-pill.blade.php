@props(['status' => null, 'label' => null, 'variant' => null])

@php
    use Illuminate\Support\Str;

    $value = null;
    $display = $label;

    if ($status !== null) {
        if ($status instanceof \BackedEnum) {
            $value = $status->value;
            $display = $display ?? (method_exists($status, 'label') ? $status->label() : Str::headline($value));
        } elseif ($status instanceof \UnitEnum) {
            $value = $status->name;
            $display = $display ?? Str::headline($value);
        } else {
            $value = (string) $status;
            $display = $display ?? Str::headline(str_replace(['_', '-'], ' ', $value));
        }
    } else {
        $display = $display ?? '';
    }

    $key = Str::lower(str_replace('-', '_', (string) ($value ?? $display)));

    $variant = $variant ?? match($key) {
        'active', 'completed', 'paid', 'published' => 'accent',
        'trialing', 'pending', 'draft'           => 'warning',
        'past_due', 'pastdue', 'past-due', 'failed', 'canceled', 'cancelled', 'suspended' => 'danger',
        default => 'muted',
    };

    // allow explicit semantic class for enum values (e.g. pill-active) plus generic pill-{variant}
    $semantic = $value ? 'pill-'.Str::lower(str_replace('-', '_', $value)) : '';
    $variantClass = 'pill-'.$variant;

    $classes = trim("status-pill {$variantClass} {$semantic}");
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $display }}</span>
