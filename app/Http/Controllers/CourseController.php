<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Services\Contracts\CourseServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseServiceInterface $courses,
        private readonly UserServiceInterface $users,
    ) {}

    public function index(): View
    {
        return view('courses.index', ['courses' => $this->courses->all()]);
    }

    public function create(): View
    {
        return view('courses.create', ['teachers' => $this->teachers()]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->organization_id !== null) {
            $data['organization_id'] = $request->user()->organization_id;
        }

        $this->courses->create($data);

        return redirect()->route('courses.index');
    }

    public function show(int $id): View
    {
        $course = $this->courses->find($id);
        abort_unless($course instanceof Course, 404);

        return view('courses.show', ['course' => $course]);
    }

    public function edit(int $id): View
    {
        $course = $this->courses->find($id);
        abort_unless($course instanceof Course, 404);

        return view('courses.edit', [
            'course' => $course,
            'teachers' => $this->teachers(),
        ]);
    }

    public function update(UpdateCourseRequest $request, int $id): RedirectResponse
    {
        $course = $this->courses->find($id);
        abort_unless($course instanceof Course, 404);

        $this->courses->update($course, $request->validated());

        return redirect()->route('courses.show', $course);
    }

    public function destroy(int $id): RedirectResponse
    {
        $course = $this->courses->find($id);
        abort_unless($course instanceof Course, 404);

        $this->courses->delete($course);

        return redirect()->route('courses.index');
    }

    private function teachers(): Collection
    {
        return $this->users->all()->where('role', UserRole::Teacher)->values();
    }
}
