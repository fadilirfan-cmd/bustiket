<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::with('pic')->latest()->paginate(10);
        return view('admin.buses.index', compact('buses'));
    }

    public function create()
    {
        $pics = User::where('role', 'pic_bus')
            ->whereNull('bus_id')
            ->get();
        return view('admin.buses.create', compact('pics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_code' => 'required|unique:buses',
            'bus_name' => 'required',
            'plate_number' => 'required|unique:buses',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:regular,vip,executive',
            'pic_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('buses', 'public');
        }

        $bus = Bus::create($validated);

        // Update PIC bus_id if assigned
        if ($validated['pic_id']) {
            User::where('id', $validated['pic_id'])->update(['bus_id' => $bus->id]);
        }

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus berhasil ditambahkan');
    }

    public function show(Bus $bus)
    {
        $bus->load(['pic', 'schedules' => function($query) {
            $query->latest()->take(10);
        }]);
        
        $statistics = [
            'total_trips' => $bus->schedules()->count(),
            'completed_trips' => $bus->schedules()->where('status', 'completed')->count(),
            'total_passengers' => $bus->schedules()->sum('booked_seats'),
            'revenue' => $bus->schedules()
                ->join('orders', 'schedules.id', '=', 'orders.schedule_id')
                ->where('orders.status', 'paid')
                ->sum('orders.total_amount')
        ];
        
        return view('admin.buses.show', compact('bus', 'statistics'));
    }

    public function edit(Bus $bus)
    {
        $pics = User::where('role', 'pic_bus')
            ->where(function($query) use ($bus) {
                $query->whereNull('bus_id')
                    ->orWhere('id', $bus->pic_id);
            })
            ->get();
            
        return view('admin.buses.edit', compact('bus', 'pics'));
    }

    public function update(Request $request, Bus $bus)
    {
        $validated = $request->validate([
            'bus_code' => 'required|unique:buses,bus_code,' . $bus->id,
            'bus_name' => 'required',
            'plate_number' => 'required|unique:buses,plate_number,' . $bus->id,
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:regular,vip,executive',
            'pic_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($bus->image) {
                Storage::disk('public')->delete($bus->image);
            }
            $validated['image'] = $request->file('image')->store('buses', 'public');
        }

        // Update PIC assignments
        if ($bus->pic_id != $validated['pic_id']) {
            // Remove old PIC
            if ($bus->pic_id) {
                User::where('id', $bus->pic_id)->update(['bus_id' => null]);
            }
            // Assign new PIC
            if ($validated['pic_id']) {
                User::where('id', $validated['pic_id'])->update(['bus_id' => $bus->id]);
            }
        }

        $bus->update($validated);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus berhasil diperbarui');
    }

    public function destroy(Bus $bus)
    {
        // Remove PIC assignment
        if ($bus->pic_id) {
            User::where('id', $bus->pic_id)->update(['bus_id' => null]);
        }
        
        // Delete image
        if ($bus->image) {
            Storage::disk('public')->delete($bus->image);
        }
        
        $bus->delete();

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus berhasil dihapus');
    }

    public function toggleStatus(Bus $bus)
    {
        $bus->status = $bus->status === 'active' ? 'inactive' : 'active';
        $bus->save();

        return response()->json([
            'success' => true,
            'status' => $bus->status,
            'message' => 'Status bus berhasil diubah'
        ]);
    }

    public function tracking(Bus $bus)
    {
        $bus->load('pic');
        
        // Get current location if available
        $currentLocation = $bus->trackings()
            ->latest()
            ->first();
        
        // Get today's route
        $todaySchedule = $bus->schedules()
            ->whereDate('departure_date', now())
            ->with('orders')
            ->first();
        
        return view('admin.buses.tracking', compact('bus', 'currentLocation', 'todaySchedule'));
    }
}