<?php

namespace App\Enums;

enum UserRole: string
{
    case SystemAdmin = 'system_admin';
    case OrgAdmin = 'org_admin';
    case Teacher = 'teacher';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::SystemAdmin => 'System Admin',
            self::OrgAdmin => 'Org Admin',
            self::Teacher => 'Teacher',
            self::Student => 'Student',
        };
    }
}
