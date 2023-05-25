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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('service_package_id');
            $table->string('registration_number');
            $table->date('registration_expire_date');
            $table->date('insurance_expire_date');
            $table->date('tax_token_expire_date');
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_model_year')->nullable();
            $table->integer('fuel_capacity', false);
            $table->float('vehicle_kpl', 8, 2);
            $table->tinyInteger('installation_status')->default(0)->comment('0 = Not Installed, 1 = Installed');
            $table->tinyInteger('payment_status')->default(0)->comment('0 = Disabled, 1 = Enabled');
            $table->tinyInteger('service_status')->default(0)->comment('0 = Not Active, 1 = Acive');
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('users');
            $table->foreign('vehicle_type_id')
                ->references('id')
                ->on('vehicle_types');
            $table->foreign('driver_id')
                ->references('id')
                ->on('users');
            $table->foreign('device_id')
                ->references('id')
                ->on('devices');
            $table->foreign('service_package_id')
                ->references('id')
                ->on('service_packages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
