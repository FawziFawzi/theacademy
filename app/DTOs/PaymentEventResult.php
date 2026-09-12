<?php

namespace App\DTOs;

readonly class PaymentEventResult
{
    public function __construct(
        public PaymentEventOutcome $outcome,
        public string $gatewayReference,
        public ?string $gatewayEventId = null,
        public array $raw = [],
    ) {}
}
