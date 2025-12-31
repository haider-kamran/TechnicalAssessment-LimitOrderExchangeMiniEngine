<?php

namespace App\Repositories;

use App\Models\Order;
use App\Enums\OrderStatusEnum;

class OrderRepository extends BaseRepository
{
    public function model(): string
    {
        return Order::class;
    }

    /**
     * Base query for listing orders
     */
    protected function fetchGetData()
    {
        return $this->model
            ->latest('id')
            ->select('*');
    }

    /**
     * Open orders for orderbook
     */
    protected function fetchOpenBySymbol(string $symbol)
    {
        return $this->model
            ->where('symbol', $symbol)
            ->where('status', OrderStatusEnum::OPEN)
            ->orderBy('price');
    }

    /**
     * First matchable sell order
     */
    protected function fetchFirstSell(string $symbol, float $price)
    {
        return $this->model
            ->where('symbol', $symbol)
            ->where('side', 'sell')
            ->where('status', OrderStatusEnum::OPEN)
            ->where('price', '<=', $price)
            ->orderBy('created_at')
            ->lockForUpdate()
            ->first();
    }

    /**
     * First matchable buy order
     */
    protected function fetchFirstBuy(string $symbol, float $price)
    {
        return $this->model
            ->where('symbol', $symbol)
            ->where('side', 'buy')
            ->where('status', OrderStatusEnum::OPEN)
            ->where('price', '>=', $price)
            ->orderBy('created_at')
            ->lockForUpdate()
            ->first();
    }

    public static function fetchGetAllOpenOrders()
    {
        return Order::where('status', OrderStatusEnum::OPEN)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
