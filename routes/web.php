<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MenuController::class, 'index'])->name('menus.index');
Route::get('/pesan/{menu}', [OrderController::class, 'create'])
    ->whereNumber('menu')
    ->name('orders.create');
Route::post('/pesan/{menu}', [OrderController::class, 'store'])
    ->middleware('throttle:10,1')
    ->whereNumber('menu')
    ->name('orders.store');
Route::get('/pembayaran/{kodePesanan}', [OrderController::class, 'payment'])
    ->whereUuid('kodePesanan')
    ->name('orders.payment');
