<?php

namespace App\Services;

use App\DTOs\PaymentEventOutcome;
use App\DTOs\PaymentEventResult;
use App\DTOs\PaymentInitiationResult;
use App\Models\Subscription;
use App\Services\Contracts\PaymentGatewayInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService implements PaymentGatewayInterface
{
    public function __construct(private readonly StripeClient $stripe) {}

    public function initiate(Subscription $subscription): PaymentInitiationResult
    {
        $subscription->loadMissing('plan');

        $plan = $subscription->plan;

        if (! $plan) {
            throw new \RuntimeException('Subscription has no plan.');
        }

        $successUrl = route('payment.success', ['subscription' => $subscription->id]);
        $cancelUrl = route('payment.cancel', ['subscription' => $subscription->id]);

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'customer_email' => $subscription->user?->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $plan->name],
                    'unit_amount' => (int) round((float) $plan->price * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'subscription_id' => (string) $subscription->id,
            ],
        ]);

        return new PaymentInitiationResult(
            redirectUrl: $session->url,
            gatewayReference: $session->id,
        );
    }

    public function handleCallback(array $requestData): PaymentEventResult
    {
        $status = $requestData['status'] ?? $requestData['checkout'] ?? null;

        if ($status === 'success') {
            return new PaymentEventResult(
                outcome: PaymentEventOutcome::Completed,
                gatewayReference: (string) ($requestData['reference'] ?? ''),
                gatewayEventId: null,
                raw: $requestData,
            );
        }

        if ($status === 'cancel') {
            return new PaymentEventResult(
                outcome: PaymentEventOutcome::Failed,
                gatewayReference: (string) ($requestData['reference'] ?? ''),
                gatewayEventId: null,
                raw: $requestData,
            );
        }

        return new PaymentEventResult(
            outcome: PaymentEventOutcome::Pending,
            gatewayReference: '',
            gatewayEventId: null,
            raw: $requestData,
        );
    }

    public function handleWebhook(string $rawPayload, string $signature): PaymentEventResult
    {
        $secret = config('stripe.webhook_secret');

        if (empty($secret)) {
            throw new \RuntimeException('Stripe webhook secret not configured.');
        }

        try {
            $event = Webhook::constructEvent($rawPayload, $signature, $secret);
        } catch (SignatureVerificationException $e) {
            throw new \RuntimeException('Stripe signature verification failed: '.$e->getMessage(), 0, $e);
        }

        $type = $event->type;
        $gatewayEventId = $event->id;
        $raw = $event->toArray();

        $outcome = match ($type) {
            'checkout.session.completed' => PaymentEventOutcome::Completed,
            'checkout.session.expired',
            'checkout.session.async_payment_failed',
            'payment_intent.payment_failed' => PaymentEventOutcome::Failed,
            default => PaymentEventOutcome::Pending,
        };

        $object = $event->data->object ?? null;

        $gatewayReference = '';
        if (is_object($object)) {
            $gatewayReference = $object->id ?? '';
            // For payment_intent events, reference still the session/payment_intent id
            if (empty($gatewayReference) && isset($object->payment_intent)) {
                $gatewayReference = is_string($object->payment_intent) ? $object->payment_intent : ($object->payment_intent->id ?? '');
            }
        } elseif (is_array($object)) {
            $gatewayReference = $object['id'] ?? '';
        }

        // Fallback to metadata subscription_id if needed? Keep gatewayReference as stripe object id
        // Subscription lookup will use stripe_session_id, so session id is the reference
        return new PaymentEventResult(
            outcome: $outcome,
            gatewayReference: (string) $gatewayReference,
            gatewayEventId: (string) $gatewayEventId,
            raw: $raw,
        );
    }
}
