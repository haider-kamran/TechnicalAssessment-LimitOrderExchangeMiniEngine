<?php

namespace App\Enums;

enum AssetSymbolEnum: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';

    public function label(): string
    {
        return match ($this) {
            self::BTC => 'Bitcoin (BTC)',
            self::ETH => 'Ethereum (ETH)',
        };
    }
}
