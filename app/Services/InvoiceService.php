<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\Contracts\InvoicePdfServiceInterface;
use App\Services\Contracts\InvoiceServiceInterface;
use Illuminate\Support\Collection;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoices,
        private readonly InvoicePdfServiceInterface $pdfService,
    ) {}

    public function all(): Collection
    {
        return $this->invoices->all();
    }

    public function find(int $id): ?Invoice
    {
        return $this->invoices->find($id);
    }

    public function createForTransaction(Transaction $transaction): Invoice
    {
        $status = $transaction->status === TransactionStatus::Completed
            ? InvoiceStatus::Paid
            : InvoiceStatus::Pending;

        $invoice = $this->invoices->create([
            'transaction_id' => $transaction->id,
            'pdf_path' => null,
            'status' => $status,
        ]);

        // Wire PDF generation to completed transactions (T13)
        if ($transaction->status === TransactionStatus::Completed) {
            $this->pdfService->configure()->generate($invoice);
            $invoice->refresh();
        }

        return $invoice;
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        return $this->invoices->updateStatus($invoice, $status);
    }
}
