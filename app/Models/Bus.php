<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $primaryKey = 'bus_id';
    
    protected $fillable = ['bus_number', 'bus_name', 'capacity', 'type'];
    
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
