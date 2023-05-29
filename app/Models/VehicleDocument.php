<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $hidden = [
        'vehicle_paper_id',
        'customer_id',
        'vehicle_id',
        'created_at',
        'updated_at',
    ];

    public function documentPaper()
    {
        return $this->belongsTo(VehiclePaper::class, 'vehicle_paper_id', 'id');
    }
}
