<?php

namespace Database\Seeders;

use App\Models\UserDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserDetails::create([
            'user_id'           => 1,
            'first_name'        => 'Admin',
            'last_name'         => 'User',
            'company_name'      => 'CPSD',
            'designation'       => 'CTO',
            'email_optional'    => 'adminopt@mail.com',
            'phone_number'      => '01552607608',
        ]);

        UserDetails::create([
            'user_id'           => 2,
            'first_name'        => 'Employee',
            'last_name'         => 'User',
            'company_name'      => 'CPSD',
            'designation'       => 'HR',
            'email_optional'    => 'employeeopt@mail.com',
            'phone_number'      => '01717546533',
        ]);

        UserDetails::create([
            'user_id'           => 3,
            'first_name'        => 'Customer',
            'phone_number'      => '01717546534',
        ]);

        UserDetails::create([
            'user_id'           => 4,
            'first_name'        => 'Driver',
            'phone_number'      => '01717546535',
        ]);
    }
}
