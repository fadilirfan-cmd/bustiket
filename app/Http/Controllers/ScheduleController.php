<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Bus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['bus', 'route']);
        
        
        // Filter by bus
       
        $query->where('bus_id', auth()->user()->bus_id);
        
        $schedules = $query->paginate(15);
        
        $buses = Bus::get();
        return request()->expectsJson()
        ? response()->json(['schedules' => $schedules])
        : view('pic.schedules');
    }

    public function create()
    {
        $buses = Bus::get();
        return view('schedules.create', compact('buses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_from' => 'required|string',
            'route_to' => 'required|string',
            'departure_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required',
            'arrival_time' => 'required|after:departure_time',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,completed,cancelled'
        ]);
        
        // Check for conflicting schedules
        $conflict = Schedule::where('bus_id', $validated['bus_id'])
            ->where('departure_date', $validated['departure_date'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('departure_time', [$validated['departure_time'], $validated['arrival_time']])
                    ->orWhereBetween('arrival_time', [$validated['departure_time'], $validated['arrival_time']]);
            })
            ->exists();
        
        if ($conflict) {
            return back()->withErrors(['departure_time' => 'Jadwal bentrok dengan jadwal yang sudah ada'])->withInput();
        }
        
        Schedule::create($validated);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['bus', 'orders.user']);
        
        $statistics = [
            'total_passengers' => $schedule->orders()->where('status', 'paid')->sum('passenger_count'),
            'total_revenue' => $schedule->orders()->where('status', 'paid')->sum('total_amount'),
            'occupancy_rate' => ($schedule->booked_seats / $schedule->bus->capacity) * 100
        ];
        
        return view('admin.schedules.show', compact('schedule', 'statistics'));
    }

    public function edit(Schedule $schedule)
    {
        $buses = Bus::where('status', 'active')->get();
        return view('admin.schedules.edit', compact('schedule', 'buses'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_from' => 'required|string',
            'route_to' => 'required|string',
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,completed,cancelled'
        ]);
        
        // Check for conflicting schedules (excluding current)
        $conflict = Schedule::where('bus_id', $validated['bus_id'])
            ->where('departure_date', $validated['departure_date'])
            ->where('id', '!=', $schedule->id)
            ->where(function($query) use ($validated) {
                $query->whereBetween('departure_time', [$validated['departure_time'], $validated['arrival_time']])
                    ->orWhereBetween('arrival_time', [$validated['departure_time'], $validated['arrival_time']]);
            })
            ->exists();
        
        if ($conflict) {
            return back()->withErrors(['departure_time' => 'Jadwal bentrok dengan jadwal yang sudah ada'])->withInput();
        }
        
        $schedule->update($validated);
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(Schedule $schedule)
    {
        // Check if there are orders
        if ($schedule->orders()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus jadwal yang sudah memiliki pesanan');
        }
        
        $schedule->delete();
        
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    public function toggleStatus(Schedule $schedule)
    {
        if ($schedule->status === 'active') {
            $schedule->status = 'inactive';
        } elseif ($schedule->status === 'inactive') {
            $schedule->status = 'active';
        }
        
        $schedule->save();
        
        return response()->json([
            'success' => true,
            'status' => $schedule->status,
            'message' => 'Status jadwal berhasil diubah'
        ]);
    }

    public function getByBus(Bus $bus)
    {
        $schedules = $bus->schedules()
            ->with('orders')
            ->orderBy('departure_date', 'desc')
            ->orderBy('departure_time', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }
}