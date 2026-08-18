<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use Illuminate\Support\Collection;

interface OrganizationRepositoryInterface
{
    public function find(int $id): ?Organization;

    public function all(): Collection;

    public function create(array $data): Organization;

    public function update(Organization $organization, array $data): Organization;

    public function delete(Organization $organization): bool;
}
