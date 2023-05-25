<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id'
    ];

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
        return $this->hasOne(ServicePackage::class, 'id', 'service_package_id');
    }

    public function deviceInfo()
    {
        return $this->hasOne(Device::class, 'id', 'device_id');
    }

    public function driverInfo()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
}
