<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->firstOrCreate([], [
            'whatsapp_message_template' => 'Halo, saya mau tanya produk {product} ({url})',
            'whatsapp_order_message_template' => 'Halo, saya mau tanya pesanan saya. Nomor: {order_number}. Status: {status_url}',
            'site_name' => 'Herbal Store',
            'site_tagline' => 'Produk herbal alami untuk keluarga Anda',
            'site_description' => 'Katalog produk herbal alami — jamu, minyak herbal, teh herbal, dan suplemen. Pesan lewat Shopee, TikTok, atau chat admin.',
            'logo_path' => $this->seedAsset('logo.svg'),
            'favicon_path' => $this->seedAsset('favicon.svg'),
            'product_placeholder_image_path' => $this->seedAsset('product-placeholder.svg'),
            'footer_text' => '© ' . now()->year . ' Herbal Store. All rights reserved.',
            'banner_image_path' => $this->seedAsset('banner.svg'),
            'banner_badge_text' => '100% Herbal Alami',
            'banner_heading' => 'Herbal Alami untuk Keluarga Sehat',
            'banner_subheading' => 'Temukan produk herbal pilihan, siap dipesan lewat Shopee, TikTok, atau chat admin.',
            'contact_email' => 'admin@herbalstore.test',
            'contact_phone' => '081234567890',
            'contact_address' => 'Jl. Merdeka No. 123, Bandung, Jawa Barat 40115',
            'instagram_url' => 'https://instagram.com/herbalstore.id',
            'facebook_url' => 'https://facebook.com/herbalstore.id',
            'tiktok_profile_url' => 'https://tiktok.com/@herbalstore.id',
            'youtube_url' => 'https://youtube.com/@herbalstore.id',
        ]);
    }

    /**
     * Copy a demo asset from database/seeders/assets into public storage and
     * return its stored path, so a fresh clone + migrate:fresh --seed always
     * reproduces the same demo branding (storage/app/public/* itself is
     * gitignored, so the source files live here instead).
     */
    private function seedAsset(string $filename): string
    {
        $source = database_path("seeders/assets/{$filename}");
        $storedPath = 'settings/' . $filename;

        Storage::disk('public')->put($storedPath, file_get_contents($source));

        return $storedPath;
    }
}
