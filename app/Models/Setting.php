<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'whatsapp_message_template',
        'whatsapp_order_message_template',
        'site_name',
        'site_tagline',
        'site_description',
        'logo_path',
        'favicon_path',
        'product_placeholder_image_path',
        'footer_text',
        'banner_image_path',
        'banner_heading',
        'banner_subheading',
        'banner_badge_text',
        'contact_email',
        'contact_phone',
        'contact_address',
        'instagram_url',
        'facebook_url',
        'tiktok_profile_url',
        'youtube_url',

        // Payment gateway (Midtrans)
        'midtrans_is_production',
        'midtrans_client_key',
        'midtrans_server_key',
        'payment_gateway_enabled',

        // Shipping (Biteship)
        'biteship_api_key',
        'biteship_is_production',
        'shipping_enabled',
        'shipping_couriers',

        // Anti-bot (reCAPTCHA v3)
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'recaptcha_enabled',
    ];

    protected function casts(): array
    {
        return [
            'midtrans_is_production' => 'boolean',
            'payment_gateway_enabled' => 'boolean',
            'biteship_is_production' => 'boolean',
            'shipping_enabled' => 'boolean',
            'recaptcha_enabled' => 'boolean',

            // Secrets — encrypted at rest, transparently decrypted in PHP,
            // never rendered back into a view (masked write-only fields).
            'midtrans_server_key' => 'encrypted',
            'biteship_api_key' => 'encrypted',
            'recaptcha_secret_key' => 'encrypted',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function hasMidtransServerKey(): bool
    {
        return filled($this->midtrans_server_key);
    }

    public function hasBiteshipApiKey(): bool
    {
        return filled($this->biteship_api_key);
    }

    public function hasRecaptchaSecretKey(): bool
    {
        return filled($this->recaptcha_secret_key);
    }
}
