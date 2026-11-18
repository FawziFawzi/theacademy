<?php

namespace App\Services;

use App\DTOs\PaymentEventOutcome;
use App\DTOs\PaymentEventResult;
use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Enums\TransactionStatus;
use App\Enums\UserRole;
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

    /**
     * Subscriptions the given user is allowed to see: students only their own,
     * org admins every subscription within their organization.
     */
    public function visibleTo(User $user): Collection
    {
        if ($user->role === UserRole::Student) {
            return $this->subscriptions->forUser($user->id);
        }

        return $this->subscriptions->all();
    }

    /**
     * Resolve a single subscription for the given viewer, returning null when a
     * student asks for someone else's subscription so the controller can 404.
     */
    public function findVisibleTo(User $user, int $id): ?Subscription
    {
        $subscription = $this->subscriptions->find($id);

        if ($subscription === null) {
            return null;
        }

        if ($user->role === UserRole::Student && $subscription->user_id !== $user->id) {
            return null;
        }

        return $subscription;
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

    /**
     * Administrative update (status and billing period), audited.
     */
    public function update(User $user, Subscription $subscription, array $data): Subscription
    {
        $old = $this->snapshot($subscription);

        $subscription = $this->subscriptions->update($subscription, $data);

        $this->auditLog->record($user, 'subscription.updated', $subscription, $old, $this->snapshot($subscription));

        return $subscription;
    }

    /**
     * Administrative delete, audited before the row is removed.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        $this->auditLog->record($user, 'subscription.deleted', $subscription, $this->snapshot($subscription), null);

        return $this->subscriptions->delete($subscription);
    }

    /**
     * @return array<string, string|null>
     */
    private function snapshot(Subscription $subscription): array
    {
        return [
            'status' => $subscription->status?->value,
            'current_period_start' => $subscription->current_period_start?->toDateString(),
            'current_period_end' => $subscription->current_period_end?->toDateString(),
        ];
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

        // 2. Resolve subscription via gatewayReference, falling back to the
        //    subscription id attached as Stripe metadata. Checkout session events
        //    reference cs_* (stored as stripe_session_id), while payment intent
        //    events reference pi_* which only correlates through metadata.
        $subscription = $this->resolveSubscription($result);

        if (! $subscription) {
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
        } elseif (isset($result->raw['data']['object']['object'], $result->raw['data']['object']['id'])
            && $result->raw['data']['object']['object'] === 'payment_intent') {
            $gatewayData['stripe_payment_intent'] = $result->raw['data']['object']['id'];
        }

        match ($result->outcome) {
            PaymentEventOutcome::Completed => $this->handleCompleted($subscription, $user, $amount, $gatewayData, $result),
            PaymentEventOutcome::Failed => $this->handleFailed($subscription, $user, $amount, $gatewayData, $result),
            PaymentEventOutcome::Pending => Log::info('Payment event pending, no state change', ['gateway_reference' => $result->gatewayReference]),
        };
    }

    private function resolveSubscription(PaymentEventResult $result): ?Subscription
    {
        if ($result->gatewayReference !== '') {
            $subscription = $this->subscriptions->findByGatewayReference($result->gatewayReference);

            if ($subscription) {
                return $subscription;
            }
        }

        $metadataSubscriptionId = $result->raw['data']['object']['metadata']['subscription_id'] ?? null;

        if (is_numeric($metadataSubscriptionId)) {
            return $this->subscriptions->find((int) $metadataSubscriptionId);
        }

        return null;
    }

    private function handleCompleted(Subscription $subscription, User $user, float $amount, array $gatewayData, PaymentEventResult $result): void
    {
        if ($subscription->status !== SubscriptionStatus::Active) {
            $this->subscriptions->updateStatus($subscription, SubscriptionStatus::Active);
            $subscription->refresh();
        }

        // Stripe sends both checkout.session.completed and payment_intent.succeeded
        // for a single payment; record the transaction only once.
        if ($this->transactionRepository->hasCompletedSubscriptionPayment($subscription->id)) {
            Log::info('Payment already recorded for subscription, skipping transaction', [
                'subscription_id' => $subscription->id,
                'gateway_event_id' => $result->gatewayEventId,
            ]);

            return;
        }

        $this->transactions->recordGatewayPayment($subscription, $amount, TransactionStatus::Completed, $gatewayData);

        $this->auditLog->record($user, 'subscription.payment_completed', $subscription, null, [
            'gateway_reference' => $result->gatewayReference,
            'gateway_event_id' => $result->gatewayEventId,
        ]);
    }

    private function handleFailed(Subscription $subscription, User $user, float $amount, array $gatewayData, PaymentEventResult $result): void
    {
        // Late failure events must not downgrade an already paid subscription.
        if ($this->transactionRepository->hasCompletedSubscriptionPayment($subscription->id)) {
            Log::info('Ignoring failed payment event, subscription already paid', [
                'subscription_id' => $subscription->id,
                'gateway_event_id' => $result->gatewayEventId,
            ]);

            return;
        }

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
