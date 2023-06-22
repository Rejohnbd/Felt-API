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
        Schema::create('device_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('device_id');
            $table->string('device_imei');
            $table->string('latitude');
            $table->string('longitude');
            $table->tinyInteger('engine_status');
            $table->string('rotation');
            $table->float('speed');
            $table->dateTime('device_time');
            $table->longText('json_data');
            $table->timestamps();

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles');

            $table->foreign('device_id')
                ->references('id')
                ->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_data');
    }
};
