<?php

namespace App\Repositories\Eloquent;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        $query = Subscription::query();

        if ($this->tenancy->isScoped()) {
            $query->whereHas('plan', fn (Builder $planQuery) => $this->tenancy->scope($planQuery));
        }

        return $query;
    }

    public function find(int $id): ?Subscription
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderByDesc('created_at')->get();
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription
    {
        $subscription->update(['status' => $status]);

        return $subscription;
    }

    public function activeForUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('status', SubscriptionStatus::Active)
            ->get();
    }
}
