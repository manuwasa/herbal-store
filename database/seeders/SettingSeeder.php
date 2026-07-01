<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->firstOrCreate([], [
            'whatsapp_number' => '6281234567890',
            'whatsapp_message_template' => 'Halo, saya mau tanya produk {product} ({url})',
            'site_name' => 'Herbal Store',
            'site_tagline' => 'Produk herbal alami untuk keluarga Anda',
            'site_description' => 'Katalog produk herbal alami — jamu, minyak herbal, teh herbal, dan suplemen. Pesan lewat Shopee, TikTok, atau chat admin.',
            'footer_text' => '© ' . now()->year . ' Herbal Store. All rights reserved.',
            'banner_heading' => 'Herbal Alami untuk Keluarga Sehat',
            'banner_subheading' => 'Temukan produk herbal pilihan, siap dipesan lewat Shopee, TikTok, atau chat admin.',
            'contact_email' => 'admin@herbalstore.test',
            'contact_phone' => '081234567890',
        ]);
    }
}
