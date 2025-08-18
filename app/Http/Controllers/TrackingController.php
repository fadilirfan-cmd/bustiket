<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function getBusLocations()
    {
        $locations = Location::with('bus')
            ->select('locations.*')
            ->join(DB::raw('(SELECT bus_id, MAX(recorded_at) as latest FROM locations GROUP BY bus_id) as latest_locations'), 
                function($join) {
                    $join->on('locations.bus_id', '=', 'latest_locations.bus_id')
                         ->on('locations.recorded_at', '=', 'latest_locations.latest');
                })
            ->get();
            
        return response()->json($locations);
    }
}
