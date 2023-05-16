<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public function customerInfo()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function vehicleType()
    {
        return $this->hasOne(VehicleType::class, 'id', 'vehicle_type_id');
    }

    public function servicePackage()
    {
        return $this->hasOne(servicePackage::class, 'id', 'service_package_id');
    }

    public function deviceInfo()
    {
        return $this->hasOne(Device::class, 'id', 'device_id');
    }
}
