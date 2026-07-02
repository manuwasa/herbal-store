<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMidtransNotification;
use App\Services\Payments\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $payload = $request->all();

        // Signature verification is the real security gate — an unverified payload
        // never reaches the queue.
        if (! $gateway->verifyWebhookSignature($payload)) {
            Log::warning('Midtrans webhook signature mismatch', [
                'order_id' => $payload['order_id'] ?? null,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'invalid signature'], 403);
        }

        // Actual DB work is queued so Midtrans gets a fast ack and safe retries.
        ProcessMidtransNotification::dispatch($payload);

        return response()->json(['message' => 'ok']);
    }
}
