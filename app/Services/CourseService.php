<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Services\Contracts\CourseServiceInterface;
use Illuminate\Support\Collection;

class CourseService implements CourseServiceInterface
{
    public function __construct(private readonly CourseRepositoryInterface $courses) {}

    public function all(): Collection
    {
        return $this->courses->all();
    }

    public function find(int $id): ?Course
    {
        return $this->courses->find($id);
    }

    public function forTeacher(int $teacherId): Collection
    {
        return $this->courses->forTeacher($teacherId);
    }

    public function create(array $data): Course
    {
        return $this->courses->create($data);
    }

    public function update(Course $course, array $data): Course
    {
        return $this->courses->update($course, $data);
    }

    public function delete(Course $course): bool
    {
        return $this->courses->delete($course);
    }
}
