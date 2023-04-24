<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $casts = [
        'package_features' => 'object'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
