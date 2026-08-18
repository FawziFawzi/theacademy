<?php

namespace App\Services\Contracts;

use App\Models\Plan;
use Illuminate\Support\Collection;

interface PlanServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Plan;

    public function create(array $data, array $courseIds = []): Plan;

    public function update(Plan $plan, array $data, ?array $courseIds = null): Plan;

    public function delete(Plan $plan): bool;
}
