<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getStatistics();
        $recentOrders = Order::with(['schedule.bus'])
            ->latest()
            ->take(10)
            ->get();
        
        $todaySchedules = Schedule::with('bus')
            ->whereDate('departure_time', Carbon::today())
            ->orderBy('departure_time')
            ->get();
        
        $busStatus = Bus::select(DB::raw('count(*) as total'))
            ->get()
            ->pluck('total');

        return view('admin.dashboard', compact('stats', 'recentOrders', 'todaySchedules', 'busStatus'));
    }
    
    public function getStats()
    {
        $stats = $this->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    private function getStatistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_buses' => Bus::count(),
            'active_buses' => Bus::count(),
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'month_orders' => Order::whereDate('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => "0",
            'today_revenue' => "0",
            'month_revenue' => "0",
            'total_schedules' => Schedule::count(),
            'today_schedules' => Schedule::whereDate('departure_time', $today)->count(),
            'total_pic' => User::where('role', 'pic_bus')->count(),
            'passengers_today' => Order::whereDate('created_at', $today),
            'chart_data' => $this->getChartData()
        ];
    }
    
    private function getChartData()
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days->push([
                'date' => $date->format('d/m'),
                'orders' => Order::whereDate('created_at', $date)->count(),
                'revenue' => "0"
            ]);
        }
        
        return $last7Days;
    }
}