<?php

namespace App\Jobs;

use App\Enums\OrderStatusEnum;
use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MatchOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {

            $order = Order::lockForUpdate()->find($this->order->id);

            if (! $order || $order->status !== OrderStatusEnum::OPEN) {
                return;
            }

            if ($order->side === 'buy') {
                $counter = Order::where('symbol', $order->symbol)
                    ->where('side', 'sell')
                    ->where('price', '<=', $order->price)
                    ->where('status', OrderStatusEnum::OPEN)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->first();
            } else {
                $counter = Order::where('symbol', $order->symbol)
                    ->where('side', 'buy')
                    ->where('price', '>=', $order->price)
                    ->where('status', OrderStatusEnum::OPEN)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->first();
            }

            if (! $counter) {
                return;
            }

            $buy  = $order->side === 'buy' ? $order : $counter;
            $sell = $order->side === 'sell' ? $order : $counter;

            $this->executeTrade($buy, $sell);
        });
    }

    protected function executeTrade(Order $buy, Order $sell): void
    {
        $amount = $buy->amount;

        // USD volume = amount × sell price
        $usdValue   = bcmul($amount, $sell->price, 8);
        $commission = bcmul($usdValue, '0.015', 8);

        $buyer = $buy->user()->lockForUpdate()->first();
        if (bccomp($buyer->balance, $commission, 8) < 0) {
            throw new \Exception('Buyer has insufficient balance for commission');
        }

        $buyer->decrement('balance', $commission);

        Asset::updateOrCreate(
            ['user_id' => $buyer->id, 'symbol' => $buy->symbol],
            ['amount' => DB::raw("amount + {$amount}")]
        );

        $seller = $sell->user()->lockForUpdate()->first();
        $sellerAsset = Asset::where('user_id', $seller->id)
            ->where('symbol', $sell->symbol)
            ->lockForUpdate()
            ->firstOrFail();

        $sellerAsset->decrement('locked_amount', $amount);
        $seller->increment('balance', $usdValue);
        $buy->update(['status' => OrderStatusEnum::FILLED]);
        $sell->update(['status' => OrderStatusEnum::FILLED]);

        Trade::create([
            'buy_order_id'  => $buy->id,
            'sell_order_id' => $sell->id,
            'symbol'        => $buy->symbol,
            'price'         => $sell->price,
            'amount'        => $amount,
            'commission'    => $commission,
        ]);

        /** REAL-TIME EVENT **/
        event(new OrderMatched($buy, $sell));
    }
}
