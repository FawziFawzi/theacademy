<x-app-layout>
    <x-form-shell
        :backHref="route('courses.show', $course)"
        backLabel="Back to Course"
        :title="'Edit ' . $course->title"
        subtitle="Update the course content and assigned instructor."
    >
        <form method="POST" action="{{ route('courses.update', $course) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <x-form-section
                    title="Course Details"
                    subtitle="Core content attributes shown across catalogs and plans."
                />

                <x-form-row for="title" label="Course Title" :required="true" hint="Descriptive title learners see in the catalog.">
                    <x-text-input id="title" name="title" type="text" :value="old('title', $course->title)" required placeholder="e.g. Introduction to Algebra" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </x-form-row>

                <x-form-row for="description" label="Description" hint="Short summary of what learners will achieve.">
                    <textarea id="description" name="description" rows="3" placeholder="What this course covers…" class="field-input resize-y">{{ old('description', $course->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </x-form-row>

                <x-form-row for="teacher_id" label="Teacher" :required="true" hint="Instructor responsible for this course.">
                    <select id="teacher_id" name="teacher_id" required class="field-input">
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected((string) old('teacher_id', $course->teacher_id) === (string) $teacher->id)>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                </x-form-row>
            </div>

            <x-form-actions :backHref="route('courses.show', $course)" submitLabel="Save changes" />
        </form>
    </x-form-shell>
</x-app-layout>
