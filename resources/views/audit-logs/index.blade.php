<x-app-layout>
    <x-slot name="header">
        <h2>Audit log</h2>
    </x-slot>

    <ul>
        @foreach ($auditLogs as $auditLog)
            <li>
                {{ $auditLog->created_at?->toDateTimeString() }}
                — {{ $auditLog->user?->name ?? 'system' }}
                — {{ $auditLog->action }}
            </li>
        @endforeach
    </ul>
</x-app-layout>