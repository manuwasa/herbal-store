<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class WhatsAppLinkBuilder
{
    public static function forProduct(Product $product): ?string
    {
        $setting = Setting::current();

        if (blank($setting->whatsapp_number)) {
            return null;
        }

        $template = $setting->whatsapp_message_template ?: 'Halo, saya mau tanya produk {product} ({url})';

        $message = str_replace(
            ['{product}', '{url}'],
            [$product->name, route('catalog.show', $product)],
            $template
        );

        return 'https://wa.me/' . $setting->whatsapp_number . '?text=' . rawurlencode($message);
    }
}
