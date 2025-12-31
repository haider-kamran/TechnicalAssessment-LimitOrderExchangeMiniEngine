<?php

namespace App\Repositories;

use App\Models\Trade;

class TradeRepository extends BaseRepository
{
    public function model(): string
    {
        return Trade::class;
    }

    /**
     * Base query for trades
     */
    public function fetchGetData()
    {
        return $this->model
            ->newQuery()
            ->orderByDesc('id');
    }

    /**
     * Create a trade record
     */
    public function createTrade(array $data): Trade
    {
        return $this->model->create($data);
    }

    /**
     * Get trades by symbol
     */
    public function fetchBySymbol(string $symbol)
    {
        return $this->fetchGetData()
            ->where('symbol', $symbol);
    }

    /**
     * Get trades for a specific user
     */
    public function fetchByUser(int $userId)
    {
        return $this->fetchGetData()
            ->whereHas('buyOrder', fn($q) => $q->where('user_id', $userId))
            ->orWhereHas('sellOrder', fn($q) => $q->where('user_id', $userId));
    }
}
