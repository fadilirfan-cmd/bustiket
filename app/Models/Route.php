<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination',
        'distance',
        'duration',
        'description',
        'waypoints',
        'base_price',
        'status',
    ];

    protected $casts = [
        'waypoints' => 'array',
        'base_price' => 'decimal:2',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
