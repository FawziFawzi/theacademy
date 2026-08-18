<?php

namespace App\Repositories\Contracts;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Support\Collection;

interface InvoiceRepositoryInterface
{
    public function find(int $id): ?Invoice;

    public function all(): Collection;

    public function create(array $data): Invoice;

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice;
}
