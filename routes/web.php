<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingRateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/katalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{product:slug}', [ProductController::class, 'show'])->name('catalog.show');

// Cart (session-based, full-page round trips).
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah', [CartController::class, 'store'])
    ->middleware('throttle:cart-write')->name('cart.store');
Route::patch('/keranjang/{product}', [CartController::class, 'update'])
    ->middleware('throttle:cart-write')->name('cart.update');
Route::delete('/keranjang/{product}', [CartController::class, 'destroy'])
    ->middleware('throttle:cart-write')->name('cart.destroy');

// Checkout.
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:checkout')->name('checkout.store');
Route::get('/checkout/{order:public_reference}/bayar', [CheckoutController::class, 'pay'])->name('checkout.pay');
Route::get('/checkout/{order:public_reference}/selesai', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Shipping AJAX (server-side proxy to Biteship — the one place fetch() is used).
Route::get('/checkout/cari-area', [ShippingRateController::class, 'searchArea'])
    ->middleware('throttle:shipping-lookup')->name('checkout.cari-area');
Route::post('/checkout/cek-ongkir', [ShippingRateController::class, 'rates'])
    ->middleware('throttle:shipping-lookup')->name('checkout.cek-ongkir');

// Durable guest order-status page (unguessable token).
Route::get('/pesanan/{order:public_reference}', [OrderStatusController::class, 'show'])->name('orders.show');
Route::get('/pesanan/{order:public_reference}/invoice', [OrderStatusController::class, 'invoice'])->name('orders.invoice');

require __DIR__ . '/admin.php';
require __DIR__ . '/webhooks.php';
