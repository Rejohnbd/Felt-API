<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    function deviceType()
    {
        return $this->hasOne(DeviceType::class, 'id', 'device_type_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function deviceLatestData()
    {
        return $this->hasOne(DeviceData::class, 'device_id', 'id')->latest();
    }
}
