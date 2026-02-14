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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->integer('room_type_id');
            $table->string('room_number');
            $table->integer('floor')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'dirty', 'cleaning', 'out_of_order'])->default('available');
            $table->timestamps();

            $table->unique(['hotel_id', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
