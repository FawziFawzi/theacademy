<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentAuditLogRepository implements AuditLogRepositoryInterface
{
    public function all(): Collection
    {
        return AuditLog::query()->orderByDesc('created_at')->get();
    }

    public function create(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}
