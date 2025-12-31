<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case OPEN = 'open';
    case FILLED = 'filled';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::FILLED => 'Filled',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'warning',
            self::FILLED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
