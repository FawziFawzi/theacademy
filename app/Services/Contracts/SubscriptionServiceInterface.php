<?php

namespace App\Services\Contracts;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

interface SubscriptionServiceInterface
{
    public function subscribe(User $user, Plan $plan): Subscription;

    public function cancel(User $user, Subscription $subscription): Subscription;

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription;
}
