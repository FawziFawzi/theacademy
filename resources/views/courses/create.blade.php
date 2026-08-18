<x-app-layout>
    <x-slot name="header">
        <h2>New course</h2>
    </x-slot>

    <form method="POST" action="{{ route('courses.store') }}">
        @csrf

        <p><label>Title <input type="text" name="title" value="{{ old('title') }}"></label></p>
        <p><label>Description <textarea name="description">{{ old('description') }}</textarea></label></p>
        <p>
            <label>Teacher
                <select name="teacher_id">
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </label>
        </p>
        <p><button type="submit">Create</button></p>
    </form>

    <p><a href="{{ route('courses.index') }}">Back</a></p>
</x-app-layout>