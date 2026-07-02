<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;

class WhatsAppLinkBuilder
{
    /**
     * Pre-checkout "Chat Admin" link on a product page. No branch is known yet,
     * so it reaches the default branch's WhatsApp number — an accepted
     * approximation (which branch fulfils an order isn't known until a
     * destination is entered at checkout).
     */
    public static function forProduct(Product $product): ?string
    {
        $number = Branch::default()?->whatsapp_number;

        if (blank($number)) {
            return null;
        }

        $setting = Setting::current();
        $template = $setting->whatsapp_message_template ?: 'Halo, saya mau tanya produk {product} ({url})';

        $message = str_replace(
            ['{product}', '{url}'],
            [$product->name, route('catalog.show', $product)],
            $template
        );

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }

    /**
     * Post-checkout order-status link. The branch is now known, so it reaches
     * the fulfilling branch's own WhatsApp number.
     */
    public static function forOrderStatus(Order $order): ?string
    {
        $number = $order->branch?->whatsapp_number ?: Branch::default()?->whatsapp_number;

        if (blank($number)) {
            return null;
        }

        $setting = Setting::current();
        $template = $setting->whatsapp_order_message_template
            ?: 'Halo, saya mau tanya pesanan saya. Nomor pesanan: {order_number}. Status: {status_url}';

        $message = str_replace(
            ['{order_number}', '{status_url}'],
            [$order->public_reference, route('orders.show', $order)],
            $template
        );

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}
