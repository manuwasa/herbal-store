<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::current();

        $data = $request->validate([
            'whatsapp_message_template' => ['nullable', 'string'],
            'whatsapp_order_message_template' => ['nullable', 'string'],
            'site_name' => ['nullable', 'string', 'max:150'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'banner_heading' => ['nullable', 'string', 'max:150'],
            'banner_subheading' => ['nullable', 'string', 'max:255'],
            'banner_badge_text' => ['nullable', 'string', 'max:60'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_address' => ['nullable', 'string'],
            'instagram_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
            'tiktok_profile_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'banner_image' => ['nullable', 'image', 'max:2048'],
            'product_placeholder_image' => ['nullable', 'image', 'max:1024'],

            // Payment gateway (Midtrans)
            'midtrans_is_production' => ['sometimes', 'boolean'],
            'payment_gateway_enabled' => ['sometimes', 'boolean'],
            'midtrans_client_key' => ['nullable', 'string', 'max:255'],
            'midtrans_server_key' => ['nullable', 'string', 'max:255'],

            // Shipping (Biteship)
            'biteship_is_production' => ['sometimes', 'boolean'],
            'shipping_enabled' => ['sometimes', 'boolean'],
            'shipping_couriers' => ['nullable', 'string', 'max:255'],
            'biteship_api_key' => ['nullable', 'string', 'max:255'],

            // Anti-bot (reCAPTCHA v3)
            'recaptcha_enabled' => ['sometimes', 'boolean'],
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['logo', 'favicon', 'banner_image', 'product_placeholder_image'] as $field) {
            if ($request->hasFile($field)) {
                $data["{$field}_path"] = $request->file($field)->store('settings', 'public');
            }
            unset($data[$field]);
        }

        // Checkbox toggles: absent = false.
        foreach (['midtrans_is_production', 'payment_gateway_enabled', 'biteship_is_production', 'shipping_enabled', 'recaptcha_enabled'] as $toggle) {
            $data[$toggle] = $request->boolean($toggle);
        }

        // Secrets are write-only: a blank submission keeps the stored value (never
        // overwrite an existing encrypted key with an empty string).
        foreach (['midtrans_server_key', 'biteship_api_key', 'recaptcha_secret_key'] as $secret) {
            if (blank($data[$secret] ?? null)) {
                unset($data[$secret]);
            }
        }

        $setting->update($data);

        return redirect()->route('admin.settings.edit')->with('status', 'Pengaturan berhasil disimpan.');
    }
}
