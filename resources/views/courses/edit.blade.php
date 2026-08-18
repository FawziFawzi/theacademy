<x-app-layout>
    <x-slot name="header">
        <h2>Edit {{ $course->title }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('courses.update', $course) }}">
        @csrf
        @method('PATCH')

        <p><label>Title <input type="text" name="title" value="{{ old('title', $course->title) }}"></label></p>
        <p><label>Description <textarea name="description">{{ old('description', $course->description) }}</textarea></label></p>
        <p>
            <label>Teacher
                <select name="teacher_id">
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected($teacher->id === $course->teacher_id)>{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </label>
        </p>
        <p><button type="submit">Save</button></p>
    </form>

    <p><a href="{{ route('courses.show', $course) }}">Back</a></p>
</x-app-layout>