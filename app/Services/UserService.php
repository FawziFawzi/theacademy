<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Support\Collection;

class UserService implements UserServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function all(): Collection
    {
        return $this->users->all();
    }

    public function find(int $id): ?User
    {
        return $this->users->find($id);
    }

    public function create(array $data): User
    {
        return $this->users->create($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->users->update($user, $data);
    }

    public function delete(User $user): bool
    {
        return $this->users->delete($user);
    }
}
