<?php

namespace App\Traits;

trait EnumHelpers
{
    public static function toArray(): array
    {
        return array_column(static::cases(), 'value', 'name');
    }
}
