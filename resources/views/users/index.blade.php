<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Users</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">New user</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">User</th>
                        <th class="text-left font-medium px-5 py-3">Email</th>
                        <th class="text-left font-medium px-5 py-3">Role</th>
                        <th class="text-right font-medium px-5 py-3">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('users.show', $user) }}" class="font-medium text-ink hover:text-accent">{{ $user->name }}</a></td>
                            <td class="px-5 py-3 text-ink-muted whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$user->role" variant="{{ in_array($user->role->value, ['system_admin','org_admin']) ? 'accent' : ($user->role->value === 'teacher' ? 'warning' : 'muted') }}" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $user->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No users found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
