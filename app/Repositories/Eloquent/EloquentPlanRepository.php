<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentPlanRepository implements PlanRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        return $this->tenancy->scope(Plan::query());
    }

    public function find(int $id): ?Plan
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderBy('name')->get();
    }

    public function create(array $data): Plan
    {
        return Plan::create($data);
    }

    public function update(Plan $plan, array $data): Plan
    {
        $plan->update($data);

        return $plan;
    }

    public function delete(Plan $plan): bool
    {
        return (bool) $plan->delete();
    }

    public function syncCourses(Plan $plan, array $courseIds): void
    {
        $plan->courses()->sync($courseIds);
    }
}
