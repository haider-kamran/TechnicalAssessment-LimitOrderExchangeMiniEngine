<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderStatusEnum;
use App\Enums\ResponseCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Jobs\MatchOrderJob;
use App\Repositories\AssetRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
        ]);

        $orders = OrderRepository::openBySymbol($request->symbol)->get();

        return $this->success(
            'Orderbook fetched successfully',
            $orders
        );
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validate([
            'symbol' => 'required|string',
            'side' => 'required|in:buy,sell',
            'price' => 'required|numeric|min:0.01',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        try {
            $order = null;

            DB::transaction(function () use ($request, $data, &$order) {
                $user = $request->user();
                $price  = number_format((float) $data['price'], 8, '.', '');
                $amount = number_format((float) $data['amount'], 8, '.', '');

                if ($data['side'] === 'buy') {
                    $total = bcmul($price, $amount, 8);

                    if (bccomp((string) $user->balance, $total, 8) < 0) {
                        throw new \Exception('Insufficient USD balance');
                    }

                    $user->decrement('balance', $total);
                }

                if ($data['side'] === 'sell') {
                    $asset = AssetRepository::lockByUserAndSymbol(
                        $user->id,
                        $data['symbol']
                    );

                    if (! $asset || bccomp((string) $asset->amount, $amount, 8) < 0) {
                        throw new \Exception('Insufficient asset balance');
                    }

                    AssetRepository::lockAmount(
                        $user->id,
                        $data['symbol'],
                        $amount
                    );
                }

                $order = OrderRepository::create([
                    'user_id' => $user->id,
                    'symbol' => $data['symbol'],
                    'side' => $data['side'],
                    'price' => $price,
                    'amount' => $amount,
                    'status' => OrderStatusEnum::OPEN,
                ]);

                dispatch(new MatchOrderJob($order));
            });

            return $this->success('Order placed successfully', $order);
        } catch (\Throwable $e) {
            return $this->error(
                $e->getMessage(),
                ResponseCodeEnum::BAD_REQUEST->value
            );
        }
    }



    public function cancel(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = OrderRepository::getById($id);

            if (!$order || $order->user_id != $user->id || $order->status !== OrderStatusEnum::OPEN) {
                return $this->error('Order cannot be cancelled', 400);
            }

            OrderRepository::cancel($order);

            return $this->success('Order cancelled successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function matchOrders()
    {
        try {
            dispatch(new \App\Jobs\MatchAllOrdersJob());

            return $this->success('Order matching triggered');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
