<?php

namespace App\Enums;

enum UserStatusEnum: string
{
    case Active = 'Active';
    case Suspended = 'Suspended';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
