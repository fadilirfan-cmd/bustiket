<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Booking;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bus_id',
        'route_id',
        'departure_time',
        'arrival_time',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'integer',
    ];

    /**
     * Get the bus that owns the schedule.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }

    /**
     * Get the route that owns the schedule.
     */
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the bookings for the schedule.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get available seats count
     */
    public function getAvailableSeatsAttribute()
    {
        $totalSeats = $this->bus->capacity;
        $bookedSeats = $this->bookings()->where('status', 'confirmed')->count();
        return $totalSeats - $bookedSeats;
    }

    /**
     * Scope a query to only include active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include upcoming schedules.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('departure_time', '>', now());
    }
}
