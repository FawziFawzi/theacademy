<?php

namespace App\Repositories\Contracts;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Support\Collection;

interface SubscriptionRepositoryInterface
{
    public function find(int $id): ?Subscription;

    public function all(): Collection;

    public function create(array $data): Subscription;

    public function update(Subscription $subscription, array $data): Subscription;

    public function delete(Subscription $subscription): bool;

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription;

    public function activeForUser(int $userId): Collection;

    public function forUser(int $userId): Collection;

    public function findByGatewayReference(string $reference): ?Subscription;
}
