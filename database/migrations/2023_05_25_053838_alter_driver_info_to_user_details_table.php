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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('license_number')->nullable()->after('image');
            $table->date('license_expire_date')->nullable()->unique()->after('license_number');
            $table->string('license_picture')->nullable()->after('license_expire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('license_number');
            $table->dropColumn('license_expire_date');
            $table->dropColumn('license_picture');
        });
    }
};
