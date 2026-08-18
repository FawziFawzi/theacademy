<x-app-layout>
    <x-slot name="header">
        <h2>{{ $course->title }}</h2>
    </x-slot>

    <p>Teacher: {{ $course->teacher?->name }}</p>
    <p>Status: {{ $course->status->label() }}</p>
    <p>{{ $course->description }}</p>

    <p><a href="{{ route('courses.edit', $course) }}">Edit</a></p>
    <p><a href="{{ route('courses.index') }}">Back</a></p>
</x-app-layout>