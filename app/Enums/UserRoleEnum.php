<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case SuperAdmin = 'SuperAdmin';
    case Admin = 'Admin';
    case Accountant = 'Accountant';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
