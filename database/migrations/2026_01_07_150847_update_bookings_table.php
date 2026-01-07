<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the old room-level columns
            if (Schema::hasColumn('bookings', 'room_id')) {
                $table->dropColumn('room_id');
            }

            if (Schema::hasColumn('bookings', 'check_in')) {
                $table->dropColumn('check_in');
            }

            if (Schema::hasColumn('bookings', 'check_out')) {
                $table->dropColumn('check_out');
            }

            // Optional: you can also add a default status if missing
            $table->enum('status', ['reserved', 'checked_in', 'checked_out', 'cancelled'])
                  ->default('reserved')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('room_id')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
        });
    }
};
