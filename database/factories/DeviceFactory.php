<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_imei'           => $this->faker->email(),
            'device_type_id'        => 1,
            'device_sim'            => $this->faker->email(),
            'device_use_status'     => 0,
            'device_health_status'  => 1,
        ];
    }
}
