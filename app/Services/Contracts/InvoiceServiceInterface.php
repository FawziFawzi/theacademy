<?php

namespace App\Services\Contracts;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Transaction;

interface InvoiceServiceInterface
{
    public function createForTransaction(Transaction $transaction): Invoice;

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice;
}
