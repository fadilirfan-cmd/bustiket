<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusLocation;

class BusTrackingController extends Controller
{
    public function index() { return view('pic.tracking'); }

public function updateLocation(Request $request) {
    $validated = $request->validate([
        'lat' => 'required|numeric',
        'lng' => 'required|numeric',
        'accuracy' => 'nullable|numeric',
        'speed' => 'nullable|numeric',
        'timestamp' => 'nullable|date',
    ]);
    $location = new BusLocation();
    $location->bus_id = auth()->user()->bus_id;
    $location->latitude = $validated['lat'];
    $location->longitude = $validated['lng'];
    $location->accuracy = $validated['accuracy'] ?? null;
    $location->speed ='0.00';
    $location->timestamp = $validated['timestamp'] ?? now();
    $location->save();
    return response()->json([
        'success' => true,
        'message' => 'Lokasi bus berhasil diperbarui',
        'data' => $location
    ]);
}
public function startTrip() { /* ... */ }
public function endTrip() { /* ... */ }
}
