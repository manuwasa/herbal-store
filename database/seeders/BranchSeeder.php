<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Seed exactly one default branch. Its shipping area is left blank on purpose
     * — there's no safe placeholder Biteship area that wouldn't risk mis-pricing
     * real shipping, so it stays blank (behaving like an unconfigured/outage
     * fallback: checkout still works, shipping is "arranged via WhatsApp") until
     * an admin fills it in via the Cabang edit form.
     */
    public function run(): void
    {
        Branch::query()->firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Cabang Utama',
                'address_detail' => '(Isi alamat lengkap cabang di halaman Cabang admin)',
                'whatsapp_number' => '6281234567890',
                'area_id' => null,
                'area_label' => null,
                'is_active' => true,
                'is_default' => true,
            ]
        );
    }
}
