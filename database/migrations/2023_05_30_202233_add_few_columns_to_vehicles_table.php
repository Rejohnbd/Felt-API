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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->tinyInteger('notification_status')->after('speed_limitation')->default(0);
            $table->tinyInteger('email_status')->after('notification_status')->default(0);
            $table->tinyInteger('over_speed_alert_status')->after('email_status')->default(0);
            $table->tinyInteger('range_alert_status')->after('over_speed_alert_status')->default(0);
            $table->tinyInteger('sms_alert_status')->after('range_alert_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('notification_status');
            $table->dropColumn('email_status');
            $table->dropColumn('over_speed_alert_status');
            $table->dropColumn('range_alert_status');
            $table->dropColumn('sms_alert_status');
        });
    }
};
