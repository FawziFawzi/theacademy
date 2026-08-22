<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">{{ $user->name }}</h2>
            <x-status-pill :status="$user->role" variant="{{ in_array($user->role->value, ['system_admin','org_admin']) ? 'accent' : ($user->role->value === 'teacher' ? 'warning' : 'muted') }}" />
        </div>
    </x-slot>

    <div class="card p-6">
        <dl class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="label">Email</dt>
                <dd class="mt-1 text-ink">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="label">Organization</dt>
                <dd class="mt-1 text-ink">{{ $user->organization?->name ?? '— (system_admin)' }}</dd>
            </div>
            <div>
                <dt class="label">Role</dt>
                <dd class="mt-1"><x-status-pill :status="$user->role" /></dd>
            </div>
            <div>
                <dt class="label">Joined</dt>
                <dd class="mt-1 tabular-nums text-ink-muted">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>
        <div class="flex items-center gap-3 mt-6">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('users.index') }}" class="btn btn-tertiary">Back</a>
        </div>
    </div>
</x-app-layout>
