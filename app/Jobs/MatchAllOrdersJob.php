<?php

namespace App\Jobs;

use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MatchAllOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $orders = OrderRepository::getAllOpenOrders();

        foreach ($orders as $order) {
            dispatch(new MatchOrderJob($order));
        }
    }
}
