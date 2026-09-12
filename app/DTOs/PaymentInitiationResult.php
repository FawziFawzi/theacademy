<?php

namespace App\DTOs;

readonly class PaymentInitiationResult
{
    public function __construct(
        public string $redirectUrl,
        public string $gatewayReference,
    ) {}
}
