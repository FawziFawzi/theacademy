<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">{{ $course->title }}</h2>
            <x-status-pill :status="$course->status" />
        </div>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="label">Teacher</dt>
                <dd class="mt-1 text-ink">{{ $course->teacher?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="label">Organization</dt>
                <dd class="mt-1 text-ink">{{ $course->organization?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="label">Status</dt>
                <dd class="mt-1"><x-status-pill :status="$course->status" /></dd>
            </div>
            <div>
                <dt class="label">Created</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $course->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
            @if($course->description)
                <div class="sm:col-span-2">
                    <dt class="label">Description</dt>
                    <dd class="mt-1 text-ink">{{ $course->description }}</dd>
                </div>
            @endif
        </dl>
        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('courses.edit', $course) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('courses.index') }}" class="btn btn-tertiary">Back</a>
        </div>
    </div>
</x-app-layout>
