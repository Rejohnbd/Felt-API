<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->unsignedBigInteger('role_id')->after('id')->default(0)->comment('0 = anonymous, 1 = admin, 2 = customer & etc');
            $table->tinyInteger('user_status')->after('email')->default(0)->comment('0 = inactive, 1 = active, 2 = suspended');
            $table->string('created_by')->after('remember_token');
            $table->softDeletes();

            $table->foreign('role_id')
                ->references('id')
                ->on('user_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->dropColumn('role_id');
            $table->dropColumn('user_status');
            $table->dropColumn('created_by');
            $table->dropColumn('deleted_at');
        });
    }
};
