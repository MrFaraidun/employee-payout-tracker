<?php

namespace App\Enums;

enum OrganizationStatusEnum: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Suspended = 'Suspended';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
