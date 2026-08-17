<?php

namespace App\Enums;

enum TransactionType: string
{
    case SubscriptionPayment = 'subscription_payment';
    case Refund = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::SubscriptionPayment => 'Subscription Payment',
            self::Refund => 'Refund',
        };
    }
}
