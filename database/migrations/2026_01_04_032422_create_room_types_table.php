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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();

            // Multi-tenant support
            $table->integer('hotel_id');

            // Room type identity
            $table->string('name'); // Deluxe, Suite, Executive
            $table->string('code')->nullable(); // DLX, STE (optional)

            // Bed & capacity
            $table->string('bed_type'); // King, Queen, Twin
            $table->unsignedTinyInteger('bed_count')->default(1);
            $table->unsignedTinyInteger('capacity'); // Max guests

            // Pricing
            $table->decimal('price_per_night', 10, 2);

            // Description & status
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Prevent duplicate room types per hotel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
