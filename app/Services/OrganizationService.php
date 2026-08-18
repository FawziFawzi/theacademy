<?php

namespace App\Services;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Services\Contracts\OrganizationServiceInterface;

class OrganizationService implements OrganizationServiceInterface
{
    public function __construct(private readonly OrganizationRepositoryInterface $organizations) {}

    public function create(array $data): Organization
    {
        return $this->organizations->create($data);
    }

    public function update(Organization $organization, array $data): Organization
    {
        return $this->organizations->update($organization, $data);
    }

    public function delete(Organization $organization): bool
    {
        return $this->organizations->delete($organization);
    }
}
