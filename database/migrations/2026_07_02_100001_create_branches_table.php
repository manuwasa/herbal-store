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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();               // short admin-facing handle, e.g. "JKT-SEL"
            $table->text('address_detail');                  // free-text street address
            $table->string('phone')->nullable();             // internal/courier contact
            $table->string('whatsapp_number')->nullable();   // customer-facing, per-branch

            // Biteship shipping origin for this branch.
            $table->string('area_id')->nullable();
            $table->string('area_label')->nullable();

            // Denormalized human-readable location, extracted from the Biteship
            // area-detail response — provider-agnostic, used for nearest-branch ranking.
            $table->string('province_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('district_name')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);   // exactly one true, enforced server-side
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
