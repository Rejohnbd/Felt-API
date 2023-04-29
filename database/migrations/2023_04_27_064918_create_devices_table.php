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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_imei', 100)->unique();
            $table->unsignedBigInteger('device_type_id')->comment('FK from table:device_types');
            $table->string('device_sim')->unique();
            $table->tinyInteger('device_sim_type')->default(1)->comment('1 == Pre Paid, 2 == Post Paid');
            $table->tinyInteger('device_use_status')->default(0)->comment('0 == Not Used, 1 == Uesed');
            $table->tinyInteger('device_health_status')->comment('0 == Spoiled, 1 == Running, 2 == Maintenance');
            $table->timestamps();

            $table->foreign('device_type_id')
                ->references('id')
                ->on('device_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
