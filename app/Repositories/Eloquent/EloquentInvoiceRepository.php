<?php

namespace App\Repositories\Eloquent;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        $query = Invoice::query();

        if ($this->tenancy->isScoped()) {
            $query->whereHas('transaction.subscription.plan', fn (Builder $planQuery) => $this->tenancy->scope($planQuery));
        }

        return $query;
    }

    public function find(int $id): ?Invoice
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderByDesc('created_at')->get();
    }

    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        $invoice->update(['status' => $status]);

        return $invoice;
    }
}
