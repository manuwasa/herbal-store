<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Seed three branches so the multi-branch admin screens (Cabang, Transfer
     * Stok, branch-scoped Pesanan/Laporan) have real-looking data out of the
     * box. `area_id`/`area_label` (the Biteship shipping origin) are left
     * blank on every branch — there's no safe placeholder Biteship area that
     * wouldn't risk mis-pricing real shipping, so it stays blank (behaving
     * like an unconfigured/outage fallback: checkout still works, shipping is
     * "arranged via WhatsApp") until an admin fills it in via the Cabang edit
     * form. `province_name`/`city_name`/`district_name` are still set — those
     * are just denormalized display/ranking fields, safe to seed directly.
     */
    public function run(): void
    {
        Branch::query()->firstOrCreate(
            ['code' => 'BDG-01'],
            [
                'name' => 'Cabang Utama Bandung',
                'address_detail' => 'Jl. Merdeka No. 123, Bandung, Jawa Barat 40115',
                'whatsapp_number' => '6281234567890',
                'province_name' => 'Jawa Barat',
                'city_name' => 'Kota Bandung',
                'district_name' => 'Sumur Bandung',
                'area_id' => null,
                'area_label' => null,
                'is_active' => true,
                'is_default' => true,
            ]
        );

        Branch::query()->firstOrCreate(
            ['code' => 'JKT-01'],
            [
                'name' => 'Cabang Jakarta Selatan',
                'address_detail' => 'Jl. Fatmawati Raya No. 45, Jakarta Selatan, DKI Jakarta 12150',
                'whatsapp_number' => '6281298765432',
                'province_name' => 'DKI Jakarta',
                'city_name' => 'Kota Jakarta Selatan',
                'district_name' => 'Cilandak',
                'area_id' => null,
                'area_label' => null,
                'is_active' => true,
                'is_default' => false,
            ]
        );

        Branch::query()->firstOrCreate(
            ['code' => 'SBY-01'],
            [
                'name' => 'Cabang Surabaya',
                'address_detail' => 'Jl. Raya Darmo No. 88, Surabaya, Jawa Timur 60241',
                'whatsapp_number' => '6281355566677',
                'province_name' => 'Jawa Timur',
                'city_name' => 'Kota Surabaya',
                'district_name' => 'Wonokromo',
                'area_id' => null,
                'area_label' => null,
                'is_active' => true,
                'is_default' => false,
            ]
        );
    }
}
