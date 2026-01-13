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
        Schema::table('payments', function (Blueprint $table) {
             // Audit
            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->after('booking_id');

            // Currency support
            $table->string('currency', 3)
                  ->default('BDT')
                  ->after('amount');

            // Payment lifecycle
            $table->enum('type', ['advance', 'balance', 'refund'])
                  ->default('advance')
                  ->after('method');

            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])
                  ->default('paid')
                  ->change();

            // Transaction reference
            $table->string('reference')
                  ->nullable()
                  ->after('status');

            // Soft delete for accounting safety
            $table->softDeletes();

            // Index for reports
            $table->index(['hotel_id', 'booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['hotel_id', 'booking_id']);

            $table->dropColumn([
                'created_by',
                'currency',
                'type',
                'reference',
                'deleted_at'
            ]);

            // Revert enum (best effort)
            $table->enum('status', ['paid', 'pending'])->change();
        });
    }
};
