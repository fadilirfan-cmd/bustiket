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
}
