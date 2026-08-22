<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="page-title">Courses</h2>
            @can('create', App\Models\Course::class)
                <a href="{{ route('courses.create') }}" class="btn btn-primary">New course</a>
            @else
                @if(in_array(auth()->user()->role?->value, ['system_admin','org_admin']))
                    <a href="{{ route('courses.create') }}" class="btn btn-primary">New course</a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Title</th>
                        <th class="text-left font-medium px-5 py-3">Teacher</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('courses.show', $course) }}" class="font-medium text-ink hover:text-accent">{{ $course->title }}</a></td>
                            <td class="px-5 py-3 text-ink-muted">{{ $course->teacher?->name ?? '—' }}</td>
                            <td class="px-5 py-3"><x-status-pill :status="$course->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $course->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No courses found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
