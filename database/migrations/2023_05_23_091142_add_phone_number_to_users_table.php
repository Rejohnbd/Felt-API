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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->after('email')->nullable()->unique();
        });

        $allUserDetails = UserDetails::all();
        foreach ($allUserDetails as $userDetails) :
            User::where('id', $userDetails->user_id)->update([
                'phone_number'  => $userDetails->phone_number
            ]);
        endforeach;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            // $table->dropUnique('users_phone_number_unique');
        });
    }
};
