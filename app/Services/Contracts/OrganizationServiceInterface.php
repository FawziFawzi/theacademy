<?php

namespace App\Services\Contracts;

use App\Models\Organization;

interface OrganizationServiceInterface
{
    public function create(array $data): Organization;

    public function update(Organization $organization, array $data): Organization;

    public function delete(Organization $organization): bool;
}
