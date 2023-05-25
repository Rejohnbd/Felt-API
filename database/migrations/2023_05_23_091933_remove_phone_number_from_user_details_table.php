<?php

use App\Models\User;
use App\Models\UserDetails;
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
            $table->dropColumn('phone_number');
            // $table->dropUnique('user_details_phone_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('phone_number')->after('email_optional')->nullable()->unique();
        });

        $allUsers = User::all();
        foreach ($allUsers as $user) :
            UserDetails::where('user_id', $user->id)->update([
                'phone_number'  => $user->phone_number
            ]);
        endforeach;
    }
};
