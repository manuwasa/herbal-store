<?php

namespace App\Services\Shipping;

interface ShippingRateProvider
{
    /**
     * @return AreaResult[]
     */
    public function searchAreas(string $query): array;

    /**
     * @return RateOption[]
     */
    public function getRates(string $originAreaId, string $destinationAreaId, int $weightGrams, int $itemsValue): array;

    public function identifier(): string;
}
