<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Contact / WhatsApp
            $table->string('whatsapp_number')->nullable();
            $table->text('whatsapp_message_template')->nullable();

            // Branding & SEO
            $table->string('site_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->text('site_description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->text('footer_text')->nullable();

            // Homepage banner
            $table->string('banner_image_path')->nullable();
            $table->string('banner_heading')->nullable();
            $table->string('banner_subheading')->nullable();

            // Footer contact / social links (each optional, shown only if set)
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('tiktok_profile_url')->nullable();
            $table->string('youtube_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
