<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Services\Contracts\CourseServiceInterface;

class CourseService implements CourseServiceInterface
{
    public function __construct(private readonly CourseRepositoryInterface $courses) {}

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
