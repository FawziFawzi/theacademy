<?php

namespace App\Services;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Services\Contracts\PlanServiceInterface;

class PlanService implements PlanServiceInterface
{
    public function __construct(private readonly PlanRepositoryInterface $plans) {}

    public function create(array $data, array $courseIds = []): Plan
    {
        $plan = $this->plans->create($data);

        if ($courseIds !== []) {
            $this->plans->syncCourses($plan, $courseIds);
        }

        return $plan;
    }

    public function update(Plan $plan, array $data, ?array $courseIds = null): Plan
    {
        $plan = $this->plans->update($plan, $data);

        if ($courseIds !== null) {
            $this->plans->syncCourses($plan, $courseIds);
        }

        return $plan;
    }

    public function delete(Plan $plan): bool
    {
        return $this->plans->delete($plan);
    }
}
