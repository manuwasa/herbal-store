<?php

namespace App\Http\Controllers;

use App\Exceptions\ShippingRateException;
use App\Models\Setting;
use App\Models\ShippingRateLookup;
use App\Services\Branches\BranchLocator;
use App\Services\Cart\CartService;
use App\Services\Shipping\ShippingRateProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingRateController extends Controller
{
    public function __construct(
        private ShippingRateProvider $provider,
        private CartService $cart,
    ) {
    }

    /**
     * Debounced area autocomplete. Proxies the provider server-side so the API
     * key never reaches the browser. Re-shaped response, never the raw provider body.
     */
    public function searchArea(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 3) {
            return response()->json(['areas' => []]);
        }

        try {
            $areas = $this->provider->searchAreas($query);
        } catch (ShippingRateException $e) {
            report($e);

            return response()->json(['areas' => []]);
        }

        return response()->json([
            'areas' => array_map(fn ($a) => [
                'id' => $a->id,
                'label' => $a->label,
                'province' => $a->provinceName,
                'city' => $a->cityName,
                'district' => $a->districtName,
            ], $areas),
        ]);
    }

    /**
     * Resolve the fulfilling branch, then quote rates from that branch's origin.
     * Records a ShippingRateLookup row (the source of truth for the price at
     * order-creation). Never hard-fails: soft-degrades to a WhatsApp-arranged
     * fallback shape the frontend already handles.
     */
    public function rates(Request $request, BranchLocator $locator): JsonResponse
    {
        $data = $request->validate([
            'destination_area_id' => ['required', 'string', 'max:100'],
            'destination_province' => ['nullable', 'string', 'max:150'],
            'destination_city' => ['nullable', 'string', 'max:150'],
            'destination_district' => ['nullable', 'string', 'max:150'],
        ]);

        if ($this->cart->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $setting = Setting::current();

        if (! $setting->shipping_enabled) {
            return response()->json(['fallback' => true, 'options' => [], 'reason' => 'shipping_disabled']);
        }

        $lines = $this->cart->items();

        $branch = $locator->findFulfillingBranch(
            $data['destination_province'] ?? null,
            $data['destination_city'] ?? null,
            $data['destination_district'] ?? null,
            $lines,
        );

        if (! $branch || blank($branch->area_id)) {
            return response()->json(['fallback' => true, 'options' => [], 'reason' => 'no_branch_can_fulfill']);
        }

        try {
            $options = $this->provider->getRates(
                $branch->area_id,
                $data['destination_area_id'],
                $this->cart->totalWeightGrams(),
                (int) round((float) $this->cart->subtotal()),
            );
        } catch (ShippingRateException $e) {
            report($e);

            return response()->json(['fallback' => true, 'options' => [], 'reason' => 'provider_down']);
        }

        if (empty($options)) {
            return response()->json(['fallback' => true, 'options' => [], 'reason' => 'no_service']);
        }

        // Record each quoted option's price so createOrder() can re-derive the
        // authoritative price without trusting the browser. One row per (cheapest)
        // selection is created lazily on selection; here we store the full set keyed
        // by courier/service via one lookup row per option.
        $lookups = [];
        foreach ($options as $option) {
            $lookup = ShippingRateLookup::create([
                'branch_id' => $branch->id,
                'session_id' => $request->session()->getId(),
                'origin_area_id' => $branch->area_id,
                'destination_area_id' => $data['destination_area_id'],
                'weight_grams' => $this->cart->totalWeightGrams(),
                'raw_request' => $data,
                'raw_response' => ['courier' => $option->courierCode, 'service' => $option->serviceCode, 'price' => $option->price],
                'selected_courier' => $option->courierCode,
                'selected_service' => $option->serviceCode,
                'selected_price' => $option->price,
                'created_at' => now(),
            ]);
            $lookups[] = $lookup;
        }

        return response()->json([
            'fallback' => false,
            'options' => array_map(fn ($o) => [
                'courier_code' => $o->courierCode,
                'courier_name' => $o->courierName,
                'service_code' => $o->serviceCode,
                'service_name' => $o->serviceName,
                'price' => $o->price,
                'duration' => $o->duration,
            ], $options),
        ]);
    }
}
