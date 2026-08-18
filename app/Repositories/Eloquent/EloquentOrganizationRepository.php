<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentOrganizationRepository implements OrganizationRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        return $this->tenancy->scope(Organization::query());
    }

    public function find(int $id): ?Organization
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderBy('name')->get();
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);

        return $organization;
    }

    public function delete(Organization $organization): bool
    {
        return (bool) $organization->delete();
    }
}
