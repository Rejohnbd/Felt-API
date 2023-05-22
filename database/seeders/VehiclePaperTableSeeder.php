<?php

namespace Database\Seeders;

use App\Models\VehiclePaper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehiclePaperTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VehiclePaper::create([
            'paper_name'        => 'Fitness Certificate',
            'paper_name_slug'   => Str::slug('Fitness Certificate')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Registration Documents',
            'paper_name_slug'   => Str::slug('Registration Documents')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Insurance Validity',
            'paper_name_slug'   => Str::slug('Insurance Validity')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Tax Token',
            'paper_name_slug'   => Str::slug('Tax Token')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Route Permit',
            'paper_name_slug'   => Str::slug('Route Permit')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Branding Permit',
            'paper_name_slug'   => Str::slug('Branding Permit')
        ]);

        VehiclePaper::create([
            'paper_name'        => 'Explosive License',
            'paper_name_slug'   => Str::slug('Explosive License')
        ]);
    }
}
