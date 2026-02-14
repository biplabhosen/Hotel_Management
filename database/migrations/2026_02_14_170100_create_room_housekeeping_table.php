<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_housekeeping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('booking_room_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->enum('task_type', ['checkout_cleaning', 'maintenance_cleaning', 'deep_cleaning'])->default('checkout_cleaning');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'room_id']);
            $table->index(['hotel_id', 'booking_id']);
            $table->index(['hotel_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_housekeeping');
    }
};
