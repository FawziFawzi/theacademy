<?php

namespace App\Services;

use App\DTOs\PaymentEventOutcome;
use App\DTOs\PaymentEventResult;
use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Enums\TransactionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Contracts\AuditLogServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\Contracts\TransactionServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionServiceInterface $transactions,
        private readonly AuditLogServiceInterface $auditLog,
    ) {}

    public function all(): Collection
    {
        return $this->subscriptions->all();
    }

    public function find(int $id): ?Subscription
    {
        return $this->subscriptions->find($id);
    }

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

    public function startCheckout(User $user, Plan $plan): Subscription
    {
        $subscription = $this->subscriptions->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Trialing,
            'current_period_start' => now()->toDateString(),
            'current_period_end' => $this->periodEnd($plan)->toDateString(),
        ]);

        $this->auditLog->record($user, 'subscription.checkout_started', $subscription, null, $subscription->toArray());

        return $subscription;
    }

    public function attachGatewayReference(Subscription $subscription, string $reference): Subscription
    {
        $subscription->update(['stripe_session_id' => $reference]);

        return $subscription->fresh();
    }

    public function applyPaymentEvent(PaymentEventResult $result): void
    {
        // 1. Idempotency: if gatewayEventId already stored, no-op
        if ($result->gatewayEventId !== null && $result->gatewayEventId !== '') {
            $existing = $this->transactionRepository->findByGatewayEventId($result->gatewayEventId);
            if ($existing) {
                Log::info('Payment event idempotent skip', ['gateway_event_id' => $result->gatewayEventId]);

                return;
            }
        }

        // 2. Resolve subscription via gatewayReference
        if (empty($result->gatewayReference)) {
            Log::warning('Payment event missing gatewayReference', ['result' => $result->raw]);

            return;
        }

        $subscription = $this->subscriptions->findByGatewayReference($result->gatewayReference);

        if (! $subscription) {
            // Also try to resolve via transaction's stripe_session_id as fallback? Spec says only findByGatewayReference
            Log::warning('Payment event subscription not found', ['gateway_reference' => $result->gatewayReference]);

            return;
        }

        $subscription->loadMissing(['plan', 'user']);

        $user = $subscription->user;

        if (! $user) {
            Log::warning('Payment event subscription has no user', ['subscription_id' => $subscription->id]);

            return;
        }

        $amount = (float) ($subscription->plan?->price ?? 0);

        $gatewayData = [
            'stripe_session_id' => $result->gatewayReference,
            'gateway_event_id' => $result->gatewayEventId,
        ];

        // Include stripe_payment_intent if available in raw payload
        if (isset($result->raw['data']['object']['payment_intent']) && is_string($result->raw['data']['object']['payment_intent'])) {
            $gatewayData['stripe_payment_intent'] = $result->raw['data']['object']['payment_intent'];
        }

        match ($result->outcome) {
            PaymentEventOutcome::Completed => $this->handleCompleted($subscription, $user, $amount, $gatewayData, $result),
            PaymentEventOutcome::Failed => $this->handleFailed($subscription, $user, $amount, $gatewayData, $result),
            PaymentEventOutcome::Pending => Log::info('Payment event pending, no state change', ['gateway_reference' => $result->gatewayReference]),
        };
    }

    private function handleCompleted(Subscription $subscription, User $user, float $amount, array $gatewayData, PaymentEventResult $result): void
    {
        if ($subscription->status !== SubscriptionStatus::Active) {
            $this->subscriptions->updateStatus($subscription, SubscriptionStatus::Active);
            $subscription->refresh();
        }

        $this->transactions->recordGatewayPayment($subscription, $amount, TransactionStatus::Completed, $gatewayData);

        $this->auditLog->record($user, 'subscription.payment_completed', $subscription, null, [
            'gateway_reference' => $result->gatewayReference,
            'gateway_event_id' => $result->gatewayEventId,
        ]);
    }

    private function handleFailed(Subscription $subscription, User $user, float $amount, array $gatewayData, PaymentEventResult $result): void
    {
        if ($subscription->status !== SubscriptionStatus::PastDue) {
            $this->subscriptions->updateStatus($subscription, SubscriptionStatus::PastDue);
            $subscription->refresh();
        }

        $this->transactions->recordGatewayPayment($subscription, $amount, TransactionStatus::Failed, $gatewayData);

        $this->auditLog->record($user, 'subscription.payment_failed', $subscription, null, [
            'gateway_reference' => $result->gatewayReference,
            'gateway_event_id' => $result->gatewayEventId,
        ]);
    }

    private function periodEnd(Plan $plan): \DateTimeInterface
    {
        return $plan->billing_interval === BillingInterval::Yearly
            ? now()->addYear()
            : now()->addMonth();
    }
}
