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
        Schema::table('device_data', function (Blueprint $table) {
            $table->float('distance', 8, 3)->after('speed')->default(0.000);
            $table->float('fuel_use', 8, 3)->after('distance')->default(0.000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_data', function (Blueprint $table) {
            $table->dropColumn('distance');
            $table->dropColumn('fuel_use');
        });
    }
};
