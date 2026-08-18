<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function find(int $id): ?Course;

    public function all(): Collection;

    public function forTeacher(int $teacherId): Collection;

    public function create(array $data): Course;

    public function update(Course $course, array $data): Course;

    public function delete(Course $course): bool;
}
