<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use Illuminate\Support\Collection;

interface PlanRepositoryInterface
{
    public function find(int $id): ?Plan;

    public function all(): Collection;

    public function create(array $data): Plan;

    public function update(Plan $plan, array $data): Plan;

    public function delete(Plan $plan): bool;

    public function syncCourses(Plan $plan, array $courseIds): void;
}
