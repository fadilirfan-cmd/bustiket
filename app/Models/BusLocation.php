<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'bus_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'timestamp',
    ];
    protected $casts = [
        'timestamp' => 'datetime',
    ];
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
