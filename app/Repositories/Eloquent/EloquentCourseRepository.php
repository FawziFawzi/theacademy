<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentCourseRepository implements CourseRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        return $this->tenancy->scope(Course::query());
    }

    public function find(int $id): ?Course
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderBy('title')->get();
    }

    public function forTeacher(int $teacherId): Collection
    {
        return $this->query()->where('teacher_id', $teacherId)->orderBy('title')->get();
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course;
    }

    public function delete(Course $course): bool
    {
        return (bool) $course->delete();
    }
}
