<x-app-layout>
    <x-slot name="header">
        <h2>New plan</h2>
    </x-slot>

    <form method="POST" action="{{ route('plans.store') }}">
        @csrf

        <p><label>Name <input type="text" name="name" value="{{ old('name') }}"></label></p>
        <p><label>Price <input type="number" step="0.01" name="price" value="{{ old('price') }}"></label></p>
        <p>
            <label>Billing interval
                <select name="billing_interval">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </label>
        </p>
        <p>Courses</p>
        <ul>
            @foreach ($courses as $course)
                <li>
                    <label>
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                            @checked(in_array($course->id, old('course_ids', []), true))>
                        {{ $course->title }}
                    </label>
                </li>
            @endforeach
        </ul>
        <p><button type="submit">Create</button></p>
    </form>

    <p><a href="{{ route('plans.index') }}">Back</a></p>
</x-app-layout>