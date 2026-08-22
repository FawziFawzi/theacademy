<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Audit log</h2>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-right font-medium px-5 py-3 whitespace-nowrap">When</th>
                        <th class="text-left font-medium px-5 py-3">Actor</th>
                        <th class="text-left font-medium px-5 py-3">Action</th>
                        <th class="text-left font-medium px-5 py-3">Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($auditLogs as $auditLog)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $auditLog->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3 text-ink">{{ $auditLog->user?->name ?? 'system' }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $auditLog->action }}</td>
                            <td class="px-5 py-3 text-ink-muted whitespace-nowrap tabular-nums">{{ class_basename($auditLog->auditable_type) }} #{{ $auditLog->auditable_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No audit entries yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
