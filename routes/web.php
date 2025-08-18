<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\HomeController;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/', [HomeController::class, 'index']);

// routes/api.php
Route::get('/schedules', [HomeController::class, 'schedules']);
Route::get('/bus-location/{bus}', function ($bus) {
    return [
        'lat'  => -6.2 + rand(-50, 50) / 1000,
        'lng'  => 106.8166 + rand(-50, 50) / 1000,
        'name' => \App\Models\Bus::find($bus)->bus_name ?? 'Bus',
    ];
});

Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');



Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/schedules/{schedule}/order', [BookingController::class, 'create'])->name('orders.create');
Route::post('/schedules/{schedule}/order', [BookingController::class, 'store'])->name('orders.store');

Route::get('api/schedules', function () {
    $routeId = request('route');
    $date    = request('date');

    $query = Schedule::with(['bus:bus_id,bus_name,type,capacity', 'route:id,origin,destination'])
        ->select('id','bus_id','route_id','departure_time','arrival_time','price')
        ->where('status','active')
        ->where('departure_time','>=', now());

    if ($routeId) {
        $query->where('route_id', $routeId);
    }
    if ($date) {
        $query->whereDate('departure_time', $date);
    }

    return $query->get()->map(function ($s) {
        $s->available_seats = $s->bus->capacity - $s->bookings()->where('status','confirmed')->count();
        return $s;
    });
});