<x-app-layout>
    <x-slot name="header">
        <h2>Edit {{ $plan->name }}</h2>
    </x-slot>

    <form method="POST" action="{{ route('plans.update', $plan) }}">
        @csrf
        @method('PATCH')

        <p><label>Name <input type="text" name="name" value="{{ old('name', $plan->name) }}"></label></p>
        <p><label>Price <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price) }}"></label></p>
        <p>
            <label>Billing interval
                <select name="billing_interval">
                    <option value="monthly" @selected($plan->billing_interval->value === 'monthly')>Monthly</option>
                    <option value="yearly" @selected($plan->billing_interval->value === 'yearly')>Yearly</option>
                </select>
            </label>
        </p>
        <p>Courses</p>
        <ul>
            @foreach ($courses as $course)
                <li>
                    <label>
                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                            @checked($plan->courses->contains('id', $course->id))>
                        {{ $course->title }}
                    </label>
                </li>
            @endforeach
        </ul>
        <p><button type="submit">Save</button></p>
    </form>

    <p><a href="{{ route('plans.show', $plan) }}">Back</a></p>
</x-app-layout>