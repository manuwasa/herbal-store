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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');                        // e.g. "midtrans"
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_order_id')->nullable();   // the order_id string we sent the gateway
            $table->string('status', 30);                     // raw gateway status
            $table->string('payment_type')->nullable();       // e.g. "qris"
            $table->decimal('gross_amount', 12, 2)->nullable();
            $table->string('snap_token')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Midtrans sends one call per status a transaction passes through
            // (pending -> settlement); only an exact-status re-delivery is a dupe.
            $table->unique(
                ['gateway', 'gateway_transaction_id', 'status'],
                'payment_tx_gateway_unique'
            );
            $table->index(['order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
