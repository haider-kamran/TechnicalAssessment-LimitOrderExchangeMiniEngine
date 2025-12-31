<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetRepository extends BaseRepository
{
    public function model(): string
    {
        return Asset::class;
    }

    /**
     * Base query for assets
     */
    protected function fetchGetData()
    {
        return $this->model
            ->latest('id')
            ->select('*');
    }

    /**
     * Get asset for a user by symbol
     */
    protected function fetchByUserAndSymbol(int $userId, string $symbol)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->first();
    }

    /**
     * Lock asset row for update (important for sell orders)
     */
    protected function fetchLockByUserAndSymbol(int $userId, string $symbol)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Increase available asset amount
     */
    protected function fetchIncreaseAmount(int $userId, string $symbol, float $amount)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->increment('amount', $amount);
    }

    /**
     * Decrease available asset amount
     */
    protected function fetchDecreaseAmount(int $userId, string $symbol, float $amount)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->decrement('amount', $amount);
    }

    /**
     * Lock asset amount (move from amount → locked_amount)
     */
    protected function fetchLockAmount(int $userId, string $symbol, float $amount)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->update([
                'amount' => DB::raw("amount - {$amount}"),
                'locked_amount' => DB::raw("locked_amount + {$amount}")
            ]);
    }

    /**
     * Unlock asset amount (move from locked_amount → amount)
     */
    protected function fetchUnlockAmount(int $userId, string $symbol, float $amount)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('symbol', $symbol)
            ->update([
                'amount' => DB::raw("amount + {$amount}"),
                'locked_amount' => DB::raw("locked_amount - {$amount}")
            ]);
    }
}
