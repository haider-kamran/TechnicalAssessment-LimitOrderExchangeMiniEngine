<?php

use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'balance']);
    Route::get('/orders', [OrdersController::class, 'index']);
    Route::post('/orders', [OrdersController::class, 'store']);
    Route::post('/orders/{id}/cancel', [OrdersController::class, 'cancel']);
    Route::post('/orders/match', [OrdersController::class, 'matchOrders']);
});
