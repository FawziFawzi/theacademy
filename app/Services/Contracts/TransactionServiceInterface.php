<?php

namespace App\Services\Contracts;

use App\Enums\TransactionStatus;
use App\Models\Subscription;
use App\Models\Transaction;

interface TransactionServiceInterface
{
    public function recordSubscriptionPayment(Subscription $subscription, float $amount): Transaction;

    public function recordRefund(Subscription $subscription, float $amount): Transaction;

    public function updateStatus(Transaction $transaction, TransactionStatus $status): Transaction;
}
