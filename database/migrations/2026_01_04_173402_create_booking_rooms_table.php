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
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->integer('booking_id');

            $table->integer('room_id');

            // Freeze price at booking time
            $table->decimal('price_per_night', 10, 2);

            // Room-level stay dates
            $table->date('check_in');
            $table->date('check_out');

            $table->timestamps();

            // Prevent duplicate room assignment in same booking
            $table->unique(['booking_id', 'room_id']);

            // Performance index for availability check
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
