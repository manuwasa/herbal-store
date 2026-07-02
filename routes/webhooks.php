<?php

use App\Http\Controllers\Webhooks\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/*
 * Payment gateway webhooks — a distinct trust boundary: neither authenticated
 * nor a normal browsing route. CSRF-exempted in bootstrap/app.php, each
 * independently signature-verified in its controller.
 */
Route::post('/webhooks/midtrans', [MidtransWebhookController::class, 'handle'])
    ->middleware('throttle:webhook-midtrans')
    ->name('webhooks.midtrans');
