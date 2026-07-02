<?php

namespace App\Services\Shipping;

class AreaResult
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly string $postalCode = '',
        public readonly ?string $provinceName = null,
        public readonly ?string $cityName = null,
        public readonly ?string $districtName = null,
    ) {
    }
}
