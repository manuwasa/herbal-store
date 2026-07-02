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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_reference')->unique();      // the only identifier ever exposed publicly
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // Guest snapshot (no customer accounts).
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('shipping_address');                // free-text street/house detail
            $table->text('customer_note')->nullable();

            // Money — all snapshotted at creation.
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->string('status', 30)->default('pending_payment');
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();

            // Dispatch (admin-filled at ship time).
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();

            // Shipping-rate snapshot (the destination + chosen quote).
            $table->string('shipping_area_id')->nullable();
            $table->string('shipping_area_label')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->string('shipping_service_label')->nullable();
            $table->unsignedInteger('shipping_weight_grams')->nullable();

            // Refund tracking (independent of `status`).
            $table->text('cancellation_reason')->nullable();
            $table->string('refund_status')->nullable();     // null | pending | refunded | failed
            $table->string('refund_id')->nullable();         // gateway refund transaction id
            $table->timestamp('refunded_at')->nullable();

            // Privacy consent evidence, per-order.
            $table->timestamp('privacy_consent_at')->nullable();

            // Transition timestamps.
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['status', 'created_at']);
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
