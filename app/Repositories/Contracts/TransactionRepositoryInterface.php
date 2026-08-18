<?php

namespace App\Repositories\Contracts;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function find(int $id): ?Transaction;

    public function all(): Collection;

    public function create(array $data): Transaction;

    public function updateStatus(Transaction $transaction, TransactionStatus $status): Transaction;
}
