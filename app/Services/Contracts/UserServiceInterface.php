<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;
}
