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
        Schema::create('shipping_rate_lookups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete(); // set once the order is created
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); // branch resolved at quote time
            $table->string('session_id')->nullable()->index(); // ties a pre-order lookup to its browser session
            $table->string('origin_area_id');
            $table->string('destination_area_id');
            $table->unsignedInteger('weight_grams');
            $table->json('raw_request')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('selected_courier')->nullable();
            $table->string('selected_service')->nullable();
            $table->decimal('selected_price', 12, 2)->nullable(); // the authoritative quoted price CheckoutService re-derives
            $table->timestamp('created_at')->nullable(); // immutable

            $table->index(['session_id', 'destination_area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rate_lookups');
    }
};
