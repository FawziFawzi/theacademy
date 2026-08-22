<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">Teaching</h2>
        <p class="text-sm text-ink-muted mt-1">Courses you teach — {{ auth()->user()->organization?->name }}</p>
    </x-slot>

    <div class="grid grid-cols-3 gap-4">
        <div class="card p-5">
            <p class="label">Courses</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['courses']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Published</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['published']) }}</p>
        </div>
        <div class="card p-5">
            <p class="label">Draft</p>
            <p class="text-2xl font-display font-bold tabular-nums mt-1">{{ number_format($stats['draft']) }}</p>
        </div>
    </div>

    <div class="card overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-line">
            <h3 class="section-heading">My courses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-ink-muted text-xs uppercase tracking-wide">
                    <tr class="border-b border-line">
                        <th class="text-left font-medium px-5 py-3">Title</th>
                        <th class="text-left font-medium px-5 py-3">Status</th>
                        <th class="text-right font-medium px-5 py-3">Plans</th>
                        <th class="text-right font-medium px-5 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($courses as $course)
                        <tr class="hover:bg-surface-raised/50 transition-colors-fast">
                            <td class="px-5 py-3"><a href="{{ route('courses.show', $course) }}" class="font-medium text-ink hover:text-accent">{{ $course->title }}</a></td>
                            <td class="px-5 py-3"><x-status-pill :status="$course->status" /></td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted">{{ $course->plans_count }}</td>
                            <td class="px-5 py-3 text-right tabular-nums text-ink-muted whitespace-nowrap">{{ $course->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">No courses assigned yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
