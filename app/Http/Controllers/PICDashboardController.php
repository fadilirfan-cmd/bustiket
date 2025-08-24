<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PICDashboardController extends Controller
{
    public function index() { return view('pic.dashboard'); }
public function myBusInfo() {
    $bus = auth()->user()->bus;
    return request()->expectsJson()
        ? response()->json(['bus' => $bus])
        : view('pic.my-bus');
}
public function schedules() {
    if (request()->expectsJson()) {
        // return data json schedules milik bus PIC
    }
    return view('pic.schedules');
}
public function todaySchedule() {
    // return JSON daftar jadwal hari ini (digunakan oleh dashboard & schedules)
}
public function passengers() {
    if (request()->expectsJson()) {
        // return JSON semua penumpang (filter by bus & maybe date)
    }
    return view('pic.passengers');
}
public function todayPassengers() {
    // return JSON penumpang hari ini
}

    

    
}
