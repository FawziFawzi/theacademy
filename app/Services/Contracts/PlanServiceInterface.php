<?php

namespace App\Services\Contracts;

use App\Models\Plan;

interface PlanServiceInterface
{
    public function create(array $data, array $courseIds = []): Plan;

    public function update(Plan $plan, array $data, ?array $courseIds = null): Plan;

    public function delete(Plan $plan): bool;
}
