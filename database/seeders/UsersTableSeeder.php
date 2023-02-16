<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'role_id'       => 2,
            'email'         => 'admin@mail.com',
            'password'      => Hash::make('password'),
            'user_status'   => 1,
            'created_by'    => 'Admin'
        ]);

        User::create([
            'role_id'       => 3,
            'email'         => 'employee@mail.com',
            'password'      => Hash::make('password'),
            'user_status'   => 1,
            'created_by'    => 'Admin'
        ]);

        User::create([
            'role_id'       => 4,
            'email'         => 'customer@mail.com',
            'password'      => Hash::make('password'),
            'user_status'   => 1,
            'created_by'    => 'Admin'
        ]);

        User::create([
            'role_id'       => 5,
            'email'         => 'driver@mail.com',
            'password'      => Hash::make('password'),
            'user_status'   => 1,
            'created_by'    => 'Admin'
        ]);
    }
}
