<?php

namespace App\Services\Contracts;

use App\DTOs\PaymentEventResult;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

interface SubscriptionServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Subscription;

    public function visibleTo(User $user): Collection;

    public function findVisibleTo(User $user, int $id): ?Subscription;

    public function subscribe(User $user, Plan $plan): Subscription;

    public function cancel(User $user, Subscription $subscription): Subscription;

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription;

    public function update(User $user, Subscription $subscription, array $data): Subscription;

    public function delete(User $user, Subscription $subscription): bool;

    public function startCheckout(User $user, Plan $plan): Subscription;

    public function attachGatewayReference(Subscription $subscription, string $reference): Subscription;

    public function applyPaymentEvent(PaymentEventResult $result): void;
}
