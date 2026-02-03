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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->integer('guest_id');
            $table->integer('room_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_guests');
            $table->enum('status', ['reserved', 'checked_in', 'checked_out', 'cancelled', 'no_show']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
