<?php

namespace App\Repositories\Contracts;

use App\Models\AuditLog;
use Illuminate\Support\Collection;

interface AuditLogRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): AuditLog;
}
