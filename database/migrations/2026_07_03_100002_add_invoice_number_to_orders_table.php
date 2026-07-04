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
        Schema::table('orders', function (Blueprint $table) {
            // Nullable: assigned by Order's creating hook, not user input. Distinct
            // from public_reference (a UUID access token) — this is the sequential,
            // human-readable number shown on a printed invoice.
            $table->string('invoice_number')->nullable()->unique()->after('public_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
};
