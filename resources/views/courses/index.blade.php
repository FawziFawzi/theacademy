<x-app-layout>
    <x-slot name="header">
        <h2>Courses</h2>
    </x-slot>

    <p><a href="{{ route('courses.create') }}">New course</a></p>

    <ul>
        @foreach ($courses as $course)
            <li>
                <a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a>
                — {{ $course->status->label() }}
            </li>
        @endforeach
    </ul>
</x-app-layout>