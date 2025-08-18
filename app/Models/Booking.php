<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat di-mass-assign
     */
    protected $fillable = [
        'schedule_id',
        'user_id',
        'passenger_name',
        'passenger_phone',
        'seat_number',
        'total_amount',
        'payment_method',
        'payment_proof',
        'status',
        'booking_date',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'booking_date' => 'datetime',
        'total_amount' => 'integer',
        'seat_number'  => 'string', // bisa berisi "1A,2B,3C"
    ];

    /**
     * Relasi ke model Schedule (jadwal keberangkatan)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Relasi ke model User (pemesan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk pemesanan yang aktif
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Accessor: kembalikan array kursi untuk kemudahan di blade
     */
    public function getSeatArrayAttribute()
    {
        return explode(',', $this->seat_number);
    }

    /**
     * Mutator: simpan kursi sebagai string terpisah koma
     */
    public function setSeatNumberAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['seat_number'] = implode(',', $value);
        } else {
            $this->attributes['seat_number'] = $value;
        }
    }

    /**
     * Cek apakah booking sudah lewat waktu keberangkatan
     */
    public function isPastDeparture()
    {
        return $this->schedule->departure_time->isPast();
    }

    /**
     * Status label untuk badge
     */
    public function statusLabel()
    {
        return match ($this->status) {
            'pending'   => 'Menunggu Pembayaran',
            'confirmed' => 'Terkonfirmasi',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            default     => 'Unknown',
        };
    }

    /**
     * Warna badge berdasarkan status
     */
    public function statusColor()
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'gray',
            default     => 'gray',
        };
    }
}
