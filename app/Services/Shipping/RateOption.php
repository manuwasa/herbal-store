<?php

namespace App\Services\Shipping;

class RateOption
{
    public function __construct(
        public readonly string $courierCode,
        public readonly string $courierName,
        public readonly string $serviceCode,
        public readonly string $serviceName,
        public readonly int $price,      // already includes shipping fee + insurance
        public readonly string $duration,
    ) {
    }
}
