<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Services\Contracts\AuditLogServiceInterface;
use Illuminate\Database\Eloquent\Model;

class AuditLogService implements AuditLogServiceInterface
{
    public function __construct(private readonly AuditLogRepositoryInterface $auditLogs) {}

    public function record(
        ?User $user,
        string $action,
        Model $auditable,
        ?array $oldValue = null,
        ?array $newValue = null,
    ): AuditLog {
        return $this->auditLogs->create([
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }
}
