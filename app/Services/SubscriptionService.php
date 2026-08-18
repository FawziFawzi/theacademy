<?php

namespace App\Services;

use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Contracts\AuditLogServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\Contracts\TransactionServiceInterface;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly TransactionServiceInterface $transactions,
        private readonly AuditLogServiceInterface $auditLog,
    ) {}

    public function subscribe(User $user, Plan $plan): Subscription
    {
        $subscription = $this->subscriptions->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active,
            'current_period_start' => now()->toDateString(),
            'current_period_end' => $this->periodEnd($plan)->toDateString(),
        ]);

        $this->transactions->recordSubscriptionPayment($subscription, (float) $plan->price);
        $this->auditLog->record($user, 'subscription.created', $subscription, null, $subscription->toArray());

        return $subscription;
    }

    public function cancel(User $user, Subscription $subscription): Subscription
    {
        $oldStatus = $subscription->status;
        $subscription = $this->subscriptions->updateStatus($subscription, SubscriptionStatus::Canceled);

        $this->auditLog->record(
            $user,
            'subscription.canceled',
            $subscription,
            ['status' => $oldStatus?->value],
            ['status' => SubscriptionStatus::Canceled->value],
        );

        return $subscription;
    }

    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription
    {
        return $this->subscriptions->updateStatus($subscription, $status);
    }

    private function periodEnd(Plan $plan): \DateTimeInterface
    {
        return $plan->billing_interval === BillingInterval::Yearly
            ? now()->addYear()
            : now()->addMonth();
    }
}
