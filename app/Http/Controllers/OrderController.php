<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['schedule.bus', 'schedule.route'])
    ->whereHas('schedule', function ($q) {
        $q->where('bus_id', auth()->user()->bus_id);
    })
    ->orderBy('created_at', 'desc')
    ->paginate(15);

        
        return request()->expectsJson()
        ? response()->json(['schedules' => $orders])
        : view('pic.passengers');
    }
    
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'passenger_name' => 'required|string|max:255',
                'passenger_phone' => 'required|string|max:15',
                'jemput' => 'required|string|max:255',
                'payment_method' => 'required|string',
                'selected_seats' => 'required|string',
                'total_price' => 'required|numeric',
            ]);
            // Generate order number jika tidak ada
            $orderNumber = $request->input('order_number') ?? 'ORD-' . strtoupper(Str::random(8));
            // Buat order baru
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->schedule_id = 14;
            $order->passenger_name = $validated['passenger_name'];
            $order->passenger_phone = $validated['passenger_phone'];
            $order->jemput = $validated['jemput'];
            $order->payment_method = $validated['payment_method'];
            $order->seat_numbers = $validated['selected_seats'];
            $order->total_price = $validated['total_price'];
            $order->status = 'pending';
            $order->save();
            // Return JSON response untuk AJAX
            return response()->json([
                'success' => true,
                'message' => 'Pemesanan berhasil',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('orders.confirmation', ['id' => $order->id])
            ]);
        } catch (\Exception $e) {
            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 422);
        }
    }
    public function confirmation($id)
    {
        $order = Order::with('schedule')->findOrFail($id);
        return view('orders.confirmation', compact('order'));
    }

    public function getBookedSeats($scheduleId)
{
    // Validasi schedule_id
    $schedule = Schedule::findOrFail($scheduleId);
    
    // Ambil semua order yang aktif (bukan cancelled) untuk jadwal ini
    $bookedSeats = Order::where('schedule_id', $scheduleId)
        ->whereIn('status', ['pending', 'confirmed']) // Hanya ambil yang belum cancelled
        ->pluck('seat_numbers') // Ambil kolom seat_numbers saja
        ->flatMap(function($seats) {
            return explode(',', $seats); // Pisahkan string "1,2,3" menjadi array [1,2,3]
        })
        ->map(function($seat) {
            return (int) trim($seat); // Convert ke integer dan hapus spasi
        })
        ->unique() // Hapus duplikat
        ->values() // Reset index array
        ->toArray();
    
    // Kembalikan response JSON
    return response()->json([
        'success' => true,
        'schedule_id' => $scheduleId,
        'booked_seats' => $bookedSeats
    ]);
}
}
