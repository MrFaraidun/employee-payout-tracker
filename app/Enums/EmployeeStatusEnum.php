<?php

namespace App\Enums;

enum EmployeeStatusEnum: string
{
    case Active = 'Active';
    case Archived = 'Archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
