<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Route;

class HomeController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['bus', 'route'])
        ->where('departure_time', '>=', now())
        ->orderBy('departure_time')
        ->paginate(9);

        $routes = Route::where('status', 'active')->get();

        return view('home', compact('schedules', 'routes'));
    }
}
