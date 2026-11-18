<?php

namespace Tests\Feature;

use App\DTOs\PaymentEventOutcome;
use App\DTOs\PaymentEventResult;
use App\Enums\SubscriptionStatus;
use App\Enums\TransactionStatus;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Contracts\SubscriptionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PaymentEventTest extends TestCase
{
    use RefreshDatabase;

    private function trialingSubscription(): Subscription
    {
        $org = Organization::factory()->create();
        $plan = Plan::factory()->for($org)->create();
        $user = User::factory()->student()->create(['organization_id' => $org->id]);

        return Subscription::factory()->trialing()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_session_123',
        ]);
    }

    private function paymentIntentEvent(Subscription $subscription, string $eventId = 'evt_test_1'): PaymentEventResult
    {
        return new PaymentEventResult(
            outcome: PaymentEventOutcome::Completed,
            gatewayReference: 'pi_test_intent_456',
            gatewayEventId: $eventId,
            raw: [
                'data' => [
                    'object' => [
                        'object' => 'payment_intent',
                        'id' => 'pi_test_intent_456',
                        'metadata' => ['subscription_id' => (string) $subscription->id],
                    ],
                ],
            ],
        );
    }

    public function test_payment_intent_event_resolves_subscription_by_metadata_and_activates_it(): void
    {
        $subscription = $this->trialingSubscription();
        $event = $this->paymentIntentEvent($subscription);

        app(SubscriptionServiceInterface::class)->applyPaymentEvent($event);

        $this->assertSame(SubscriptionStatus::Active, $subscription->fresh()->status);

        $this->assertDatabaseHas('transactions', [
            'subscription_id' => $subscription->id,
            'status' => TransactionStatus::Completed->value,
            'stripe_payment_intent' => 'pi_test_intent_456',
        ]);
    }

    public function test_checkout_session_event_resolves_subscription_by_session_id(): void
    {
        $subscription = $this->trialingSubscription();

        app(SubscriptionServiceInterface::class)->applyPaymentEvent(new PaymentEventResult(
            outcome: PaymentEventOutcome::Completed,
            gatewayReference: 'cs_test_session_123',
            gatewayEventId: 'evt_test_session',
            raw: [
                'data' => [
                    'object' => [
                        'object' => 'checkout.session',
                        'id' => 'cs_test_session_123',
                        'payment_intent' => 'pi_test_intent_456',
                        'metadata' => ['subscription_id' => (string) $subscription->id],
                    ],
                ],
            ],
        ));

        $this->assertSame(SubscriptionStatus::Active, $subscription->fresh()->status);
    }

    public function test_second_event_for_the_same_payment_does_not_duplicate_the_transaction(): void
    {
        $subscription = $this->trialingSubscription();
        $service = app(SubscriptionServiceInterface::class);

        // Stripe delivers both events for one payment.
        $service->applyPaymentEvent($this->paymentIntentEvent($subscription, 'evt_test_intent'));

        $service->applyPaymentEvent(new PaymentEventResult(
            outcome: PaymentEventOutcome::Completed,
            gatewayReference: 'cs_test_session_123',
            gatewayEventId: 'evt_test_session',
            raw: [
                'data' => [
                    'object' => [
                        'object' => 'checkout.session',
                        'id' => 'cs_test_session_123',
                        'metadata' => ['subscription_id' => (string) $subscription->id],
                    ],
                ],
            ],
        ));

        $this->assertSame(1, Transaction::where('subscription_id', $subscription->id)->count());
        $this->assertSame(SubscriptionStatus::Active, $subscription->fresh()->status);
    }

    public function test_unresolvable_event_leaves_subscription_trialing(): void
    {
        $subscription = $this->trialingSubscription();

        app(SubscriptionServiceInterface::class)->applyPaymentEvent(new PaymentEventResult(
            outcome: PaymentEventOutcome::Completed,
            gatewayReference: 'pi_unknown',
            gatewayEventId: 'evt_test_unknown',
            raw: ['data' => ['object' => ['object' => 'payment_intent', 'id' => 'pi_unknown']]],
        ));

        $this->assertSame(SubscriptionStatus::Trialing, $subscription->fresh()->status);
        $this->assertSame(0, Transaction::where('subscription_id', $subscription->id)->count());
    }

    private function postSignedWebhook(array $eventArray): TestResponse
    {
        config(['stripe.webhook_secret' => 'whsec_test']);

        $payload = json_encode($eventArray);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        return $this->call('POST', '/payment/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        ], $payload);
    }

    public function test_signed_payment_intent_webhook_activates_the_subscription(): void
    {
        $subscription = $this->trialingSubscription();

        $response = $this->postSignedWebhook([
            'id' => 'evt_webhook_intent',
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_webhook_intent',
                'object' => 'payment_intent',
                'metadata' => ['subscription_id' => (string) $subscription->id],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame(SubscriptionStatus::Active, $subscription->fresh()->status);
    }

    public function test_signed_checkout_session_completed_webhook_activates_the_subscription(): void
    {
        $subscription = $this->trialingSubscription();

        $response = $this->postSignedWebhook([
            'id' => 'evt_webhook_session',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'id' => 'cs_test_session_123',
                'object' => 'checkout.session',
                'payment_intent' => 'pi_webhook_session',
                'metadata' => ['subscription_id' => (string) $subscription->id],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame(SubscriptionStatus::Active, $subscription->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'subscription_id' => $subscription->id,
            'stripe_payment_intent' => 'pi_webhook_session',
        ]);
    }
}
