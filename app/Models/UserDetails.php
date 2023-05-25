<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'driver_id', 'user_id');
    }
}
