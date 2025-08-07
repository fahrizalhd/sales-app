<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'SUPERADMIN';
    case USER = 'USER';
    case ADMIN = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Admin',
            self::USER => 'User',
            self::ADMIN => 'Admin',
        };
    }   
}