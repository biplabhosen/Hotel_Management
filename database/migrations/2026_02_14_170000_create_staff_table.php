<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->enum('role', ['housekeeping', 'manager', 'front_desk'])->default('housekeeping');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['hotel_id', 'status']);
            $table->unique(['hotel_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
