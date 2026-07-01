<?php

namespace App\Enums;

enum PayoutStatusEnum: string
{
    case Pending = 'Pending';
    case Processing = 'Processing';
    case Completed = 'Completed';
    case Failed = 'Failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
