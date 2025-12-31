<?php

namespace App\Models;

use App\Enums\AssetSymbolEnum;
use App\Enums\OrderSideEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'symbol', 'side', 'price', 'amount', 'status'];
    protected $casts = [
    'symbol' => AssetSymbolEnum::class,
    'side'   => OrderSideEnum::class,
    'status' => OrderStatusEnum::class,
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }
}
