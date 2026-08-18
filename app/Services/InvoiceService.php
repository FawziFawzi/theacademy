<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\Contracts\InvoiceServiceInterface;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoices) {}

    public function createForTransaction(Transaction $transaction): Invoice
    {
        $status = $transaction->status === TransactionStatus::Completed
            ? InvoiceStatus::Paid
            : InvoiceStatus::Pending;

        return $this->invoices->create([
            'transaction_id' => $transaction->id,
            'pdf_path' => null,
            'status' => $status,
        ]);
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        return $this->invoices->updateStatus($invoice, $status);
    }
}
