<?php

namespace App\Services\Contracts;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

interface SubscriptionServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Subscription;

    public function subscribe(User $user, Plan $plan): Subscription;

    public function cancel(User $user, Subscription $subscription): Subscription;

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription;
}
