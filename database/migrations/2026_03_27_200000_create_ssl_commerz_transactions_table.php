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
        Schema::create('ssl_commerz_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('transaction_id', 40)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->enum('status', ['Pending', 'Processing', 'Complete', 'Failed', 'Canceled'])->default('Pending');
            $table->enum('payment_type', ['advance', 'balance'])->default('balance');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'booking_id']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssl_commerz_transactions');
    }
};
