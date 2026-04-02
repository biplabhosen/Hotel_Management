<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_id', 40)->nullable()->after('reference');
        });

        // Add 'sslcommerz' to the method enum
        DB::statement("ALTER TABLE `" . DB::getTablePrefix() . "payments` MODIFY `method` ENUM('cash','card','mobile_banking','bank_transfer','sslcommerz') NOT NULL DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });

        DB::statement("ALTER TABLE `" . DB::getTablePrefix() . "payments` MODIFY `method` ENUM('cash','card','mobile_banking','bank_transfer') NOT NULL DEFAULT 'cash'");
    }
};
