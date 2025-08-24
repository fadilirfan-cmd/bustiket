<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusLocation;
use App\Models\Bus;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function getBusLocations($busId)
    {
        try {
            // Ambil lokasi terbaru berdasarkan bus_id
            $latestLocation = BusLocation::where('bus_id', $busId)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$latestLocation) {
                // Jika tidak ada data lokasi, return error
                return response()->json([
                    'error' => 'No location data found'
                ], 404);
            }
            
            // Cari informasi bus (opsional, jika tabel bus ada)
            $bus = Bus::find($busId);
            $busName = $bus ? ($bus->bus_name ?? "Bus #{$busId}") : "Bus #{$busId}";
            
            // Format response sesuai dengan yang diharapkan oleh JavaScript
            return response()->json([
                'name' => $busName,
                'lat' => (float) $latestLocation->latitude,
                'lng' => (float) $latestLocation->longitude,
                'accuracy' => (float) $latestLocation->accuracy,
                'speed' => (float) $latestLocation->speed,
                'last_update' => Carbon::parse($latestLocation->created_at)->diffForHumans(),
                'timestamp' => $latestLocation->timestamp
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch bus location',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBusRoute($busId)
    {
        try {
            // Ambil semua lokasi dalam 24 jam terakhir
            $locations = BusLocation::where('bus_id', $busId)
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->orderBy('created_at', 'asc')
                ->get(['latitude', 'longitude', 'created_at', 'speed', 'accuracy']);
            
            // Format untuk polyline di Leaflet
            $route = $locations->map(function($loc) {
                return [
                    'lat' => (float) $loc->latitude,
                    'lng' => (float) $loc->longitude,
                    'time' => $loc->created_at->format('H:i:s'),
                    'speed' => (float) $loc->speed
                ];
            });
            
            return response()->json([
                'bus_id' => $busId,
                'total_points' => $route->count(),
                'route' => $route
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch bus route',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
