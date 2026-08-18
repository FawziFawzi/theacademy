<?php

namespace App\Repositories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;

class TenantContext
{
    public function organizationId(): ?int
    {
        $user = auth()->user();

        if ($user === null || $user->role === UserRole::SystemAdmin) {
            return null;
        }

        return $user->organization_id;
    }

    public function isScoped(): bool
    {
        return $this->organizationId() !== null;
    }

    public function scope(Builder $query): Builder
    {
        $organizationId = $this->organizationId();

        if ($organizationId === null) {
            return $query;
        }

        return $query->where('organization_id', $organizationId);
    }
}
