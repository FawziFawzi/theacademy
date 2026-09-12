<?php

namespace App\Http\Controllers;

use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly SubscriptionServiceInterface $subscriptions,
    ) {}

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        if (empty($signature)) {
            return response('Missing signature', 400);
        }

        try {
            $result = $this->gateway->handleWebhook($payload, $signature);
        } catch (\RuntimeException $e) {
            // SignatureVerificationException is wrapped as RuntimeException in StripeService
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'signature') || str_contains($msg, 'webhook secret')) {
                Log::warning('Payment webhook signature verification failed', ['error' => $e->getMessage()]);

                return response('Invalid signature', 400);
            }
            Log::warning('Payment webhook misconfigured', ['error' => $e->getMessage()]);

            return response('Webhook misconfigured', 400);
        } catch (\Throwable $e) {
            Log::error('Payment webhook unexpected failure', ['error' => $e->getMessage()]);

            return response('Webhook handling failed', 500);
        }

        try {
            $this->subscriptions->applyPaymentEvent($result);
        } catch (\Throwable $e) {
            Log::error('Payment apply failed', ['error' => $e->getMessage(), 'gateway_event_id' => $result->gatewayEventId]);

            return response('Apply failed', 500);
        }

        return response('OK', 200);
    }
}
