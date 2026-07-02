<?php

namespace App\Services\Shipping;

use App\Exceptions\ShippingRateException;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class BiteshipProvider implements ShippingRateProvider
{
    private const BASE_URL = 'https://api.biteship.com/v1';

    public function __construct(private Setting $setting)
    {
    }

    public function identifier(): string
    {
        return 'biteship';
    }

    public function searchAreas(string $query): array
    {
        try {
            $response = Http::withHeaders(['authorization' => $this->apiKey()])
                ->acceptJson()
                ->timeout(8)
                ->get(self::BASE_URL . '/maps/areas', [
                    'countries' => 'ID',
                    'input' => $query,
                    'type' => 'single',
                ]);
        } catch (\Throwable $e) {
            throw new ShippingRateException('Biteship area search transport error: ' . $e->getMessage());
        }

        if (! $response->successful() || ! ($response->json('success') ?? false)) {
            throw new ShippingRateException('Biteship area search failed: ' . $response->body());
        }

        return array_map(
            fn (array $a) => new AreaResult(
                id: $a['id'],
                label: $a['name'] ?? '',
                postalCode: (string) ($a['postal_code'] ?? ''),
                provinceName: $a['administrative_division_level_1_name'] ?? null,
                cityName: $a['administrative_division_level_2_name'] ?? null,
                districtName: $a['administrative_division_level_3_name'] ?? null,
            ),
            $response->json('areas') ?? []
        );
    }

    public function getRates(string $originAreaId, string $destinationAreaId, int $weightGrams, int $itemsValue): array
    {
        try {
            $response = Http::withHeaders(['authorization' => $this->apiKey()])
                ->acceptJson()
                ->timeout(8)
                ->post(self::BASE_URL . '/rates/couriers', [
                    'origin_area_id' => $originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'couriers' => $this->setting->shipping_couriers ?: 'jne,jnt,sicepat,anteraja',
                    'items' => [[
                        'name' => 'Barang',
                        'value' => max($itemsValue, 0),
                        'quantity' => 1,
                        'weight' => max($weightGrams, 1), // Biteship mis-prices a zero weight
                    ]],
                ]);
        } catch (\Throwable $e) {
            throw new ShippingRateException('Biteship rate check transport error: ' . $e->getMessage());
        }

        if (! $response->successful() || ! ($response->json('success') ?? false)) {
            throw new ShippingRateException('Biteship rate check failed: ' . $response->body());
        }

        $options = array_map(
            fn (array $p) => new RateOption(
                courierCode: $p['courier_code'] ?? '',
                courierName: $p['courier_name'] ?? '',
                serviceCode: $p['courier_service_code'] ?? '',
                serviceName: $p['courier_service_name'] ?? '',
                price: (int) ($p['price'] ?? 0),
                duration: (string) ($p['duration'] ?? ''),
            ),
            $response->json('pricing') ?? []
        );

        // Cheapest first — so the pre-selected option is the least expensive.
        usort($options, fn (RateOption $a, RateOption $b) => $a->price <=> $b->price);

        return $options;
    }

    private function apiKey(): string
    {
        return (string) $this->setting->biteship_api_key;
    }
}
