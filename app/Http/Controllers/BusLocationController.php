<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusLocation;

class BusLocationController extends Controller
{
    public function showInputPage($busId)
    {
        $bus = Bus::findOrFail($busId);
        return view('bus.location-tracker', compact('bus'));
    }
    /**
     * Menyimpan data lokasi bus
     */
    public function store(Request $request, $busId)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'timestamp' => 'nullable|date',
        ]);
        $location = new BusLocation();
        $location->bus_id = $busId;
        $location->latitude = $validated['latitude'];
        $location->longitude = $validated['longitude'];
        $location->accuracy = $validated['accuracy'] ?? null;
        $location->speed = $validated['speed'] ?? null;
        $location->timestamp = $validated['timestamp'] ?? now();
        $location->save();
        return response()->json([
            'success' => true,
            'message' => 'Lokasi bus berhasil diperbarui',
            'data' => $location
        ]);
    }
}
