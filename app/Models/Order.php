<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_number',
        'schedule_id',
        'passenger_name',
        'passenger_phone',
        'jemput',
        'payment_method',
        'seat_numbers',
        'total_price',
        'status',
    ];
    // Relasi dengan model Schedule
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
