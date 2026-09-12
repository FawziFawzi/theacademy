<?php

namespace App\Services\Contracts;

use App\DTOs\PaymentEventResult;
use App\DTOs\PaymentInitiationResult;
use App\Models\Subscription;

interface PaymentGatewayInterface
{
    public function initiate(Subscription $subscription): PaymentInitiationResult;

    public function handleCallback(array $requestData): PaymentEventResult;

    public function handleWebhook(string $rawPayload, string $signature): PaymentEventResult;
}
