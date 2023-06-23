<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'device_id',
        'device_imei',
        'latitude',
        'longitude',
        'engine_status',
        'rotation',
        'speed',
        'distance',
        'fuel_use',
        'device_time',
        'json_data'
    ];
}
