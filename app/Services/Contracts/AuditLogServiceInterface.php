<?php

namespace App\Services\Contracts;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface AuditLogServiceInterface
{
    public function record(
        ?User $user,
        string $action,
        Model $auditable,
        ?array $oldValue = null,
        ?array $newValue = null,
    ): AuditLog;
}
