<?php

namespace App\Repositories\Eloquent;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(private readonly TenantContext $tenancy) {}

    private function query(): Builder
    {
        $query = Transaction::query();

        if ($this->tenancy->isScoped()) {
            $query->whereHas('subscription.plan', fn (Builder $planQuery) => $this->tenancy->scope($planQuery));
        }

        return $query;
    }

    public function find(int $id): ?Transaction
    {
        return $this->query()->find($id);
    }

    public function all(): Collection
    {
        return $this->query()->orderByDesc('created_at')->get();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateStatus(Transaction $transaction, TransactionStatus $status): Transaction
    {
        $transaction->update(['status' => $status]);

        return $transaction;
    }
}
