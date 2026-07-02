<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Account-level payment/shipping/anti-bot config lives on the Setting
     * singleton. Location-specific config (shipping origin, per-branch
     * WhatsApp number) lives on `branches` instead — so the global
     * `whatsapp_number` column is dropped here, superseded by branch.whatsapp_number.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Payment gateway (Midtrans)
            $table->boolean('midtrans_is_production')->default(false);
            $table->string('midtrans_client_key')->nullable(); // public — safe in frontend JS
            $table->text('midtrans_server_key')->nullable();    // secret — encrypted cast on the model
            $table->boolean('payment_gateway_enabled')->default(false);
            $table->text('whatsapp_order_message_template')->nullable();

            // Shipping (Biteship)
            $table->text('biteship_api_key')->nullable();        // secret — encrypted cast
            $table->boolean('biteship_is_production')->default(false); // label only; key prefix selects mode
            $table->boolean('shipping_enabled')->default(false);
            $table->string('shipping_couriers')->nullable();     // comma-separated courier codes

            // Anti-bot (reCAPTCHA v3)
            $table->string('recaptcha_site_key')->nullable();    // public — safe in frontend JS
            $table->text('recaptcha_secret_key')->nullable();    // secret — encrypted cast
            $table->boolean('recaptcha_enabled')->default(false);
        });

        // Superseded by per-branch whatsapp_number.
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable();

            $table->dropColumn([
                'midtrans_is_production',
                'midtrans_client_key',
                'midtrans_server_key',
                'payment_gateway_enabled',
                'whatsapp_order_message_template',
                'biteship_api_key',
                'biteship_is_production',
                'shipping_enabled',
                'shipping_couriers',
                'recaptcha_site_key',
                'recaptcha_secret_key',
                'recaptcha_enabled',
            ]);
        });
    }
};
