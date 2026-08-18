<?php

namespace App\Services\Contracts;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface AuditLogServiceInterface
{
    public function all(): Collection;

    public function record(
        ?User $user,
        string $action,
        Model $auditable,
        ?array $oldValue = null,
        ?array $newValue = null,
    ): AuditLog;
}
