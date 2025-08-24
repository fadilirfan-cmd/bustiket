<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusTrackingController extends Controller
{
    public function index() { return view('pic.tracking'); }
public function getCurrentTrip() {
    // Kembalikan JSON seperti:
    // return response()->json([
    //   'status' => 'on_trip'|'idle',
    //   'started_at' => now()->subMinutes(25),
    //   'latest_location' => ['lat'=>-6.2,'lng'=>106.8,'updated_at'=>now()],
    //   'points' => [ ['lat'=>-6.2,'lng'=>106.8], ... ]
    // ]);
}
public function updateLocation(Request $r) {
    // Terima lat,lng,(accuracy,speed)
}
public function startTrip() { /* ... */ }
public function endTrip() { /* ... */ }
}
