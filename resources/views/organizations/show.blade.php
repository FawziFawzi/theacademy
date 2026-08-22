<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="page-title">{{ $organization->name }}</h2>
            <x-status-pill :status="$organization->status" />
        </div>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="label">Email</dt>
                <dd class="mt-1 text-ink">{{ $organization->email }}</dd>
            </div>
            <div>
                <dt class="label">Phone</dt>
                <dd class="mt-1 text-ink tabular-nums">{{ $organization->phone ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="label">Address</dt>
                <dd class="mt-1 text-ink">{{ $organization->address ?? '—' }}</dd>
            </div>
            <div>
                <dt class="label">Created</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $organization->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="label">Status</dt>
                <dd class="mt-1"><x-status-pill :status="$organization->status" /></dd>
            </div>
        </dl>

        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('organizations.index') }}" class="btn btn-tertiary">Back</a>
        </div>
    </div>
</x-app-layout>
