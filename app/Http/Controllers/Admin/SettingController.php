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
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'whatsapp_message_template' => ['nullable', 'string'],
            'site_name' => ['nullable', 'string', 'max:150'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'banner_heading' => ['nullable', 'string', 'max:150'],
            'banner_subheading' => ['nullable', 'string', 'max:255'],
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
        ]);

        foreach (['logo', 'favicon', 'banner_image'] as $field) {
            if ($request->hasFile($field)) {
                $data["{$field}_path"] = $request->file($field)->store('settings', 'public');
            }
            unset($data[$field]);
        }

        $setting->update($data);

        return redirect()->route('admin.settings.edit')->with('status', 'Pengaturan berhasil disimpan.');
    }
}
