<?php

namespace App\Services\Contracts;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Collection;

interface InvoiceServiceInterface
{
    public function all(): Collection;

    public function find(int $id): ?Invoice;

    public function createForTransaction(Transaction $transaction): Invoice;

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice;
}
