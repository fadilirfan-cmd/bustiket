<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class BookingController extends Controller
{
    public function create(Schedule $schedule)
{
    $schedule->load(['bus', 'route', 'bookings']);
    return view('orders.create', compact('schedule'));
}

public function store(Request $request, Schedule $schedule)
{
    $request->validate([
        'passenger_name' => 'required|string|max:255',
        'passenger_phone' => 'required|string|max:20',
        'seat_numbers' => 'required|string',
        'payment_method' => 'required|in:transfer_bca,transfer_bni,transfer_mandiri,tunai_agen',
    ]);

    $seats = explode(',', $request->seat_numbers);
    $total = count($seats) * $schedule->price;

    $booking = $schedule->bookings()->create([
        'user_id' => auth()->id,
        'passenger_name' => $request->passenger_name,
        'passenger_phone' => $request->passenger_phone,
        'seat_number' => $request->seat_numbers,
        'total_amount' => $total,
        'payment_method' => $request->payment_method,
        'status' => 'pending',
    ]);

    return redirect()->route('bookings.show', $booking)
                     ->with('success', 'Pemesanan berhasil dibuat. Silakan lakukan pembayaran.');
}
}
