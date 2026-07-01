<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'whatsapp_number',
        'whatsapp_message_template',
        'site_name',
        'site_tagline',
        'site_description',
        'logo_path',
        'favicon_path',
        'footer_text',
        'banner_image_path',
        'banner_heading',
        'banner_subheading',
        'contact_email',
        'contact_phone',
        'contact_address',
        'instagram_url',
        'facebook_url',
        'tiktok_profile_url',
        'youtube_url',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
