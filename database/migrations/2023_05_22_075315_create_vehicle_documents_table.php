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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('FK from table:users');
            $table->unsignedBigInteger('vehicle_id')->comment('FK from table:vehicles');
            $table->unsignedBigInteger('vehicle_paper_id')->comment('FK from table:vehicle_papers');
            $table->date('expire_date');
            $table->string('document_image')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('users');
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles');
            $table->foreign('vehicle_paper_id')
                ->references('id')
                ->on('vehicle_papers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
