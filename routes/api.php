<?
use Illuminate\Support\Facades\Route;
use App\Models\Schedule;

Route::get('/schedules', function () {
    $routeId = request('route');
    $date    = request('date');

    $query = \App\Models\Schedule::with(['bus:id,bus_name,type,capacity', 'route:id,origin,destination'])
        ->select('schedules.*')
        ->where('schedules.status', 'active')
        ->where('schedules.departure_time', '>=', now());

    // **exact match** berdasarkan ID, bukan string
    if ($routeId && $routeId !== '') {
        $query->where('schedules.route_id', $routeId);
    }

    if ($date) {
        $query->whereDate('schedules.departure_time', $date);
    }

    return $query->get()->map(fn($s) => [
        ...$s->toArray(),
        'available_seats' => $s->bus->capacity - $s->bookings()->where('status', 'confirmed')->count()
    ]);
});