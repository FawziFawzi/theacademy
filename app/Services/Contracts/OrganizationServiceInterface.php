<?php

namespace App\Services\Contracts;

use App\Models\Organization;
use Illuminate\Support\Collection;

interface OrganizationServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Organization;

    public function create(array $data): Organization;

    public function update(Organization $organization, array $data): Organization;

    public function delete(Organization $organization): bool;
}
