<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">{{ $plan->name }}</h2>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="label">Price</dt>
                <dd class="mt-1 text-xl font-display font-bold tabular-nums">${{ number_format($plan->price, 2) }}</dd>
            </div>
            <div>
                <dt class="label">Billing interval</dt>
                <dd class="mt-1"><span class="status-pill pill-muted">{{ $plan->billing_interval->label() }}</span></dd>
            </div>
            <div>
                <dt class="label">Created</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $plan->created_at?->format('Y-m-d') }}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <h3 class="section-heading mb-3">Courses</h3>
            @if($plan->courses->isEmpty())
                <p class="text-sm text-ink-muted">No courses attached</p>
            @else
                <ul class="divide-y divide-line rounded-card overflow-hidden border border-line">
                    @foreach ($plan->courses as $course)
                        <li class="px-4 py-3 flex items-center justify-between bg-surface">
                            <a href="{{ route('courses.show', $course) }}" class="font-medium text-ink hover:text-accent">{{ $course->title }}</a>
                            <x-status-pill :status="$course->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('plans.edit', $plan) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('plans.index') }}" class="btn btn-tertiary">Back</a>
        </div>
    </div>
</x-app-layout>
