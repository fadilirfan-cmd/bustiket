<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\HomeController;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;
// Atau jika menggunakan Laravel 8+
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Http\Controllers\BusLocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PICDashboardController;
use App\Http\Controllers\BusTrackingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BusApiController;
use App\Http\Controllers\TrackingApiController;

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk halaman input lokasi bus
Route::get('/api/bus-location/{bus_id}', [BusLocationController::class, 'showInputPage'])->name('bus-location.show');
// Route untuk menyimpan lokasi bus
Route::post('/api/bus-location/{bus_id}', [BusLocationController::class, 'store'])->name('bus-location.store');

Route::get('/api/schedules/{id}/booked-seats', [OrderController::class, 'getBookedSeats'])->name('api.schedules.booked-seats');

Route::post('/process-booking', [OrderController::class, 'store'])->name('orders.store');

// Route untuk halaman konfirmasi
Route::get('/booking/confirmation/{id}', [OrderController::class, 'confirmation'])->name('orders.confirmation');

// routes/web.php
Route::get('/', [HomeController::class, 'index']);

// routes/api.php
Route::get('/schedules', [HomeController::class, 'schedules']);


Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

Route::get('/tracking/bus/{bus_id}', [TrackingController::class, 'getBusLocations'])->name('tracking.index');
Route::get('/tracking/bus/{bus_id}/route', [TrackingController::class, 'getBusRoute'])->name('tracking.route');



Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/schedules/{schedule}/order', [BookingController::class, 'create'])->name('orders.create');

Route::get('api/schedules', function () {
    $routeId = request('route');
    $date    = request('date');

    $query = Schedule::with(['bus:bus_id,bus_name,type,capacity', 'route:id,origin,destination'])
        ->select('id','bus_id','route_id','departure_time','arrival_time','price')
        ->where('status','active');

    if ($routeId) {
        $query->where('route_id', $routeId);
    }
    if ($date) {
        $query->whereDate('departure_time', $date);
    }
    // Ambil semua jadwal yang memenuhi kriteria
    $schedules = $query->get();
    
    // Ambil schedule_id dari hasil query
    $scheduleIds = $schedules->pluck('id')->toArray();
    
    // Ambil data order untuk schedule_id yang ada dalam hasil query
    $orders = Order::whereIn('schedule_id', $scheduleIds)
        ->whereIn('status', ['pending', 'confirmed']) // Hanya status aktif
        ->select('schedule_id', 'seat_numbers')
        ->get();
    
    // Buat array untuk menyimpan jumlah kursi terpesan per jadwal
    $bookedSeatsCount = [];
    
    // Hitung jumlah kursi terpesan untuk setiap jadwal
    foreach ($orders as $order) {
        $seatCount = count(explode(',', $order->seat_numbers));
        
        if (!isset($bookedSeatsCount[$order->schedule_id])) {
            $bookedSeatsCount[$order->schedule_id] = 0;
        }
        
        $bookedSeatsCount[$order->schedule_id] += $seatCount;
    }
    
    // Map hasil akhir dengan jumlah kursi yang tersedia
    return $schedules->map(function ($schedule) use ($bookedSeatsCount) {
        // Ambil jumlah kursi terpesan untuk jadwal ini (default 0 jika tidak ada)
        $bookedSeats = $bookedSeatsCount[$schedule->id] ?? 0;
        
        // Hitung kursi tersedia
        $schedule->available_seats = $schedule->bus->capacity - $bookedSeats;
        
        // Tambahkan info kursi terpesan
        $schedule->booked_seats = $bookedSeats;
        
        return $schedule;
    });

});

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});
// Logout Route (Authenticated Only)

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');
    
    // Bus Management
    Route::resource('buses', BusController::class);
    Route::post('/buses/{bus}/toggle-status', [BusController::class, 'toggleStatus'])->name('buses.toggle-status');
    Route::get('/buses/{bus}/tracking', [BusController::class, 'tracking'])->name('buses.tracking');
    
    // Schedule Management
    Route::resource('schedules', ScheduleController::class);
    Route::post('/schedules/{schedule}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');
    Route::get('/schedules/bus/{bus}', [ScheduleController::class, 'getByBus'])->name('schedules.by-bus');
    
    // Order Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
    Route::get('/orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
    
    // Bus Tracking (Admin View)
    Route::get('/tracking', [BusTrackingController::class, 'adminIndex'])->name('tracking.index');
    Route::get('/tracking/all-buses', [BusTrackingController::class, 'getAllBusLocations'])->name('tracking.all');
});
/*
|--------------------------------------------------------------------------
| PIC Bus Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('pic')->name('pic.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PICDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/my-bus', [PICDashboardController::class, 'myBusInfo'])->name('dashboard.my-bus');
    
    // Bus Tracking & Location Update
    Route::get('/tracking', [BusTrackingController::class, 'index'])->name('tracking.index');
    Route::post('/tracking/update-location', [BusTrackingController::class, 'updateLocation'])->name('tracking.update');
    Route::post('/tracking/start-trip', [BusTrackingController::class, 'startTrip'])->name('tracking.start');
    Route::post('/tracking/end-trip', [BusTrackingController::class, 'endTrip'])->name('tracking.end');
    Route::get('/tracking/current-trip', [BusTrackingController::class, 'getCurrentTrip'])->name('tracking.current');
    
    // Schedule View (Read Only)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules');
    //Route::get('/schedules/today', [PICDashboardController::class, 'todaySchedule'])->name('schedules.today');
    
    // Passenger List
    Route::get('/order', [OrderController::class, 'index'])->name('order');
    
    
});
/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

// Authentication API Routes (No Auth Required)
Route::prefix('api/auth')->group(function () {
    Route::post('/send-otp', [LoginController::class, 'sendOTP']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOTP']);
    Route::post('/resend-otp', [LoginController::class, 'resendOTP']);
    Route::get('/user', function () {
        return "Hai";
    });

});
// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user()->load('bus');
    });
    
    // Admin API Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Bus Management API
        Route::apiResource('buses', BusApiController::class);
        Route::get('/buses/search/{query}', [BusApiController::class, 'search']);
        
        // Real-time Tracking API
        Route::get('/tracking/buses', [TrackingApiController::class, 'getAllBuses']);
        Route::get('/tracking/bus/{bus}', [TrackingApiController::class, 'getBusLocation']);
        Route::get('/tracking/bus/{bus}/route', [TrackingApiController::class, 'getBusRoute']);
        
        // Statistics API
        Route::get('/stats/dashboard', [BusApiController::class, 'dashboardStats']);
        Route::get('/stats/buses', [BusApiController::class, 'busStats']);
        Route::get('/stats/orders', [BusApiController::class, 'orderStats']);
    });
    
    // PIC API Routes
    Route::middleware('role:pic_bus')->prefix('pic')->group(function () {
        // Location Update API
        Route::post('/location/update', [TrackingApiController::class, 'updateLocation']);
        Route::get('/location/current', [TrackingApiController::class, 'getCurrentLocation']);
        
        // Trip Management API
        Route::post('/trip/start', [TrackingApiController::class, 'startTrip']);
        Route::post('/trip/end', [TrackingApiController::class, 'endTrip']);
        Route::get('/trip/current', [TrackingApiController::class, 'currentTrip']);
        
        // Bus Status API
        Route::post('/bus/status', [TrackingApiController::class, 'updateBusStatus']);
        Route::get('/bus/info', [TrackingApiController::class, 'getBusInfo']);
    });
});
// Public API Routes (Optional - for mobile app or external access)
Route::prefix('public')->group(function () {
    Route::get('/buses/active', [BusApiController::class, 'getActiveBuses']);
    Route::get('/schedules/today', [BusApiController::class, 'getTodaySchedules']);
    Route::get('/tracking/live/{bus_code}', [TrackingApiController::class, 'publicTracking']);
});