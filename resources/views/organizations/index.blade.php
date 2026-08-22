<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Organizations</h2>
            <a href="{{ route('organizations.create') }}" class="btn btn-primary">New organization</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Organization</th>
                        <th class="text-left font-medium px-5 py-3">Contact</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($organizations as $organization)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3">
                                <a href="{{ route('organizations.show', $organization) }}" class="font-medium text-ink hover:text-accent">{{ $organization->name }}</a>
                            </td>
                            <td class="px-5 py-3 text-ink-muted whitespace-nowrap">{{ $organization->email }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$organization->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $organization->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No organizations found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
