<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        @page { margin: 32px 28px; }
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 10pt; color: #0E1614; line-height: 1.5; }
        .header { border-bottom: 2px solid #2FBFA8; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 18pt; font-weight: 700; letter-spacing: -0.02em; }
        .brand-accent { color: #2FBFA8; }
        .tagline { color: #8FA39E; font-size: 7pt; letter-spacing: 0.18em; text-transform: uppercase; margin-top: 2px; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .meta td { padding: 6px 0; vertical-align: top; }
        .label { color: #8FA39E; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.08em; }
        .value { color: #0E1614; font-weight: 600; }
        .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 8pt; font-weight: 600; background: #173B35; color: #EAF2F0; }
        .pill-paid { background: #173B35; color: #2FBFA8; }
        .pill-pending { background: #3a3320; color: #E0B23D; }
        .pill-failed { background: #3d2320; color: #E5695E; }
        .card { border: 1px solid #263330; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; background: #ffffff; }
        .card-title { font-size: 9pt; font-weight: 700; color: #0E1614; margin-bottom: 8px; border-bottom: 1px solid #EAF2F0; padding-bottom: 6px; }
        .table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .table th { text-align: left; color: #8FA39E; font-weight: 600; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.06em; padding: 6px 8px; border-bottom: 1px solid #263330; }
        .table td { padding: 7px 8px; border-bottom: 1px solid #e8efed; }
        .text-right { text-align: right; }
        .tabular { font-variant-numeric: tabular-nums; }
        .footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #263330; color: #8FA39E; font-size: 7.5pt; text-align: center; }
        .totals td { font-weight: 700; border-top: 2px solid #0E1614; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand"><span class="brand-accent">The</span>Academy</div>
        <div class="tagline">Learn The Way</div>
        <table class="meta">
            <tr>
                <td style="width: 60%;">
                    @if($organization)
                        <div class="value">{{ $organization->name }}</div>
                        <div style="color:#8FA39E; font-size:8.5pt;">{{ $organization->email }}@if($organization->phone) · {{ $organization->phone }}@endif</div>
                        @if($organization->address)<div style="color:#8FA39E; font-size:8.5pt;">{{ $organization->address }}</div>@endif
                    @endif
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="label">Invoice</div>
                    <div class="value" style="font-size: 14pt;">#{{ $invoice->id }}</div>
                    <div class="label" style="margin-top:6px;">Status</div>
                    <div><span class="pill pill-{{ strtolower($invoice->status->value) }}">{{ $invoice->status->label() }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 50%;">
                <div class="label">Billed to</div>
                @if($user)
                    <div class="value">{{ $user->name }}</div>
                    <div style="color:#8FA39E; font-size:8.5pt;">{{ $user->email }}</div>
                @else
                    <div class="value">—</div>
                @endif
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="label">Invoice date</div>
                <div class="value tabular">{{ $invoice->created_at?->format('Y-m-d H:i') }}</div>
                <div class="label" style="margin-top:6px;">Transaction</div>
                <div class="value tabular">#{{ $transaction?->id ?? '—' }}</div>
            </td>
        </tr>
    </table>

    <div class="card">
        <div class="card-title">Transaction details</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($plan) {{ $plan->name }}@else —@endif
                        @if($plan)<div style="color:#8FA39E; font-size:7.5pt;">{{ $plan->billing_interval->label() }}</div>@endif
                    </td>
                    <td>{{ $transaction?->type->label() ?? '—' }}</td>
                    <td>{{ $transaction?->status->label() ?? '—' }}</td>
                    <td class="text-right tabular">${{ number_format($transaction?->amount ?? 0, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="totals">
                    <td colspan="3" class="text-right">Total</td>
                    <td class="text-right tabular">${{ number_format($transaction?->amount ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($subscription)
    <div class="card">
        <div class="card-title">Subscription</div>
        <table class="table">
            <tr>
                <th>Subscription</th>
                <td class="tabular">#{{ $subscription->id }} — {{ $subscription->status->label() }}</td>
            </tr>
            <tr>
                <th>Period</th>
                <td class="tabular">{{ $subscription->current_period_start?->format('Y-m-d') }} → {{ $subscription->current_period_end?->format('Y-m-d') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        © {{ $invoice->created_at?->format('Y') }} The Academy · Learn The Way · Invoice generated on {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
