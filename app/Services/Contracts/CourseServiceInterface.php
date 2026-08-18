<?php

namespace App\Services\Contracts;

use App\Models\Course;

interface CourseServiceInterface
{
    public function create(array $data): Course;

    public function update(Course $course, array $data): Course;

    public function delete(Course $course): bool;
}
