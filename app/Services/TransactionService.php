<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use App\Services\Contracts\TransactionServiceInterface;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
        private readonly InvoiceServiceInterface $invoices,
    ) {}

    public function recordSubscriptionPayment(Subscription $subscription, float $amount): Transaction
    {
        return $this->record($subscription, TransactionType::SubscriptionPayment, $amount);
    }

    public function recordRefund(Subscription $subscription, float $amount): Transaction
    {
        return $this->record($subscription, TransactionType::Refund, $amount);
    }

    public function updateStatus(Transaction $transaction, TransactionStatus $status): Transaction
    {
        return $this->transactions->updateStatus($transaction, $status);
    }

    private function record(Subscription $subscription, TransactionType $type, float $amount): Transaction
    {
        $transaction = $this->transactions->create([
            'subscription_id' => $subscription->id,
            'type' => $type,
            'amount' => $amount,
            'status' => TransactionStatus::Completed,
        ]);

        $this->invoices->createForTransaction($transaction);

        return $transaction;
    }
}
