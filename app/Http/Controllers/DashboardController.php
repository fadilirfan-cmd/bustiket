<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_buses' => Bus::count(),
            'active_buses' => Bus::where('status', 'active')->count(),
            'total_schedules' => Schedule::count(),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'total_revenue' => Order::where('status', 'confirmed')->sum('total_amount'),
        ];
        $recent_orders = Order::with(['user', 'schedule.bus'])
            ->latest()
            ->limit(5)
            ->get();
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }
    // Bus Management
    public function busIndex()
    {
        $buses = Bus::latest()->paginate(10);
        return view('admin.buses.index', compact('buses'));
    }
    public function busStore(Request $request)
    {
        $validated = $request->validate([
            'bus_code' => 'required|unique:buses',
            'bus_name' => 'required',
            'plate_number' => 'required|unique:buses',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive'
        ]);
        Bus::create($validated);
        return redirect()->route('admin.buses')
            ->with('success', 'Bus berhasil ditambahkan');
    }
    public function busUpdate(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'bus_name' => 'required',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive'
        ]);
        $bus->update($validated);
        return redirect()->route('admin.buses')
            ->with('success', 'Bus berhasil diperbarui');
    }
    public function busDestroy(Bus $bus)
    {
        $bus->delete();
        return redirect()->route('admin.buses')
            ->with('success', 'Bus berhasil dihapus');
    }
    // Schedule Management
    public function scheduleIndex()
    {
        $schedules = Schedule::with('bus')->latest()->paginate(10);
        $buses = Bus::where('status', 'active')->get();
        
        return view('admin.schedules.index', compact('schedules', 'buses'));
    }
    public function scheduleStore(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_from' => 'required',
            'route_to' => 'required',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array',
            'status' => 'required|in:active,cancelled'
        ]);
        $validated['days_of_week'] = json_encode($validated['days_of_week']);
        Schedule::create($validated);
        return redirect()->route('admin.schedules')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }
    public function scheduleUpdate(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_from' => 'required',
            'route_to' => 'required',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'price' => 'required|numeric|min:0',
            'days_of_week' => 'required|array',
            'status' => 'required|in:active,cancelled'
        ]);
        $validated['days_of_week'] = json_encode($validated['days_of_week']);
        $schedule->update($validated);
        return redirect()->route('admin.schedules')
            ->with('success', 'Jadwal berhasil diperbarui');
    }
    public function scheduleDestroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules')
            ->with('success', 'Jadwal berhasil dihapus');
    }
    // Order Management
    public function orderIndex()
    {
        $orders = Order::with(['user', 'schedule.bus'])
            ->latest()
            ->paginate(15);
            
        return view('admin.orders.index', compact('orders'));
    }
    public function orderUpdate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);
        $order->update($validated);
        return redirect()->route('admin.orders')
            ->with('success', 'Status order berhasil diperbarui');
    }
    // Bus Location Tracking
    public function locationIndex()
    {
        $buses = Bus::where('status', 'active')->get();
        $locations = [];
        foreach ($buses as $bus) {
            $location = BusLocation::where('bus_id', $bus->id)
                ->latest()
                ->first();
                
            if ($location) {
                $locations[] = [
                    'bus' => $bus,
                    'location' => $location,
                    'last_update' => $location->created_at->diffForHumans()
                ];
            }
        }
        return view('admin.locations.index', compact('locations'));
    }
    public function locationHistory(Bus $bus)
    {
        $locations = BusLocation::where('bus_id', $bus->id)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'bus' => $bus,
            'locations' => $locations
        ]);
    }
}
