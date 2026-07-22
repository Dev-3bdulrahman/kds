<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Kds\Http\Controllers\Api\KdsApiController;

Route::prefix('api/v1/kds')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    Route::get('displays/{display}/active-orders', [KdsApiController::class, 'getActiveOrders'])->middleware('can:kds.orders.view')->name('api.v1.kds.active-orders');
    Route::put('items/{item}/status', [KdsApiController::class, 'updateItemStatus'])->middleware('can:kds.orders.update-status')->name('api.v1.kds.items.update-status');
    Route::put('orders/{order}/status', [KdsApiController::class, 'updateOrderStatus'])->middleware('can:kds.orders.update-status')->name('api.v1.kds.orders.update-status');
});
