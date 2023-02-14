<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRole::create([
            'name'          => 'Anonymous',
            'slug'          => Str::slug('Anonymous'),
        ]);

        UserRole::create([
            'name'          => 'Admin',
            'slug'          => Str::slug('Admin')
        ]);

        UserRole::create([
            'name'          => 'Employee',
            'slug'          => Str::slug('Employee')
        ]);

        UserRole::create([
            'name'          => 'Customer',
            'slug'          => Str::slug('Customer')
        ]);

        UserRole::create([
            'name'          => 'Driver',
            'slug'          => Str::slug('Driver')
        ]);
    }
}
