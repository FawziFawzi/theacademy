<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Subscription #<span class="tabular-nums">{{ $subscription->id }}</span></h2>
            <x-status-pill :status="$subscription->status" />
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="section-heading mb-4">Details</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="label">Plan</dt><dd class="font-medium text-ink">{{ $subscription->plan?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="label">Organization</dt><dd class="text-ink">{{ $subscription->plan?->organization?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="label">Price</dt><dd class="tabular-nums font-medium">${{ number_format($subscription->plan?->price ?? 0, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="label">Student</dt><dd class="text-ink">{{ $subscription->user?->name }}</dd></div>
                <div class="flex justify-between"><dt class="label">Status</dt><dd><x-status-pill :status="$subscription->status" /></dd></div>
                <div class="flex justify-between"><dt class="label">Period</dt><dd class="tabular-nums text-ink-muted">{{ $subscription->current_period_start?->format('Y-m-d') }} → {{ $subscription->current_period_end?->format('Y-m-d') }}</dd></div>
                @if($subscription->stripe_session_id)
                    <div class="flex justify-between"><dt class="label">Stripe session</dt><dd class="tabular-nums text-xs text-ink-muted">{{ $subscription->stripe_session_id }}</dd></div>
                @endif
            </dl>
            <div class="flex items-center gap-3 mt-6">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-tertiary">Back</a>
                @if(auth()->user()->role?->value === 'student' && in_array($subscription->status->value, ['active','trialing']))
                    <form method="POST" action="{{ route('subscriptions.cancel', $subscription) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Cancel</button>
                    </form>
                @endif
                @if(auth()->user()->role?->value === 'system_admin')
                    <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-secondary">Edit</a>
                    <form method="POST" action="{{ route('subscriptions.destroy', $subscription) }}" onsubmit="return confirm('Delete this subscription permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card p-6">
            <h3 class="section-heading mb-4">Invoices</h3>
            @if($subscription->transactions?->isEmpty())
                <p class="text-sm text-ink-muted">No transactions yet</p>
            @else
                <ul class="space-y-2">
                    @foreach($subscription->transactions as $tx)
                        <li class="flex items-center justify-between p-3 rounded-card bg-surface-raised border border-line">
                            <span class="tabular-nums font-medium">#{{ $tx->id }}</span>
                            <span class="tabular-nums">${{ number_format($tx->amount, 2) }}</span>
                            <x-status-pill :status="$tx->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
