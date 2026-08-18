<?php

namespace App\Services\Contracts;

use App\Enums\TransactionStatus;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Collection;

interface TransactionServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Transaction;

    public function recordSubscriptionPayment(Subscription $subscription, float $amount): Transaction;

    public function recordRefund(Subscription $subscription, float $amount): Transaction;

    public function updateStatus(Transaction $transaction, TransactionStatus $status): Transaction;
}
