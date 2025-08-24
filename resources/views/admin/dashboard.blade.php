@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Dashboard Admin</h1>
        <p class="text-gray-400 mt-2">Selamat datang kembali, {{ auth()->user()->name }}</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Bus --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Bus</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_buses'] }}</p>
                    <p class="text-green-500 text-sm mt-2">
                        <span class="font-semibold">{{ $stats['active_buses'] }}</span> Aktif
                    </p>
                </div>
                <div class="bg-red-600/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Today Orders --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Pesanan Hari Ini</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['today_orders'] }}</p>
                    <p class="text-gray-400 text-sm mt-2">
                        Total: <span class="text-white font-semibold">{{ $stats['total_orders'] }}</span>
                    </p>
                </div>
                <div class="bg-blue-600/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4h2a1 1 0 100 2 2 2 0 01-2 2H4a2 2 0 01-2-2V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Today Revenue --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($stats['today_revenue'], 0, ',', '.') }}
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        Bulan ini: <span class="text-green-500 font-semibold">{{ number_format($stats['month_revenue'], 0, ',', '.') }}</span>
                    </p>
                </div>
                <div class="bg-green-600/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Passengers Today --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Penumpang Hari Ini</p>
            
                    <p class="text-gray-400 text-sm mt-2">
                        <span class="font-semibold">{{ $stats['today_schedules'] }}</span> Jadwal
                    </p>
                </div>
                <div class="bg-purple-600/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Today's Schedules --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 mb-8">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">Jadwal Hari Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Bus</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Rute</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Keberangkatan</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Kursi</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todaySchedules as $schedule)
                    <tr class="border-b border-gray-700">
                        <td class="px-6 py-4 text-sm text-white">
                            {{ $schedule->bus->bus_code }} - {{ $schedule->bus->bus_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $schedule->route_from }} → {{ $schedule->route_to }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            {{ $schedule->booked_seats }}/{{ $schedule->bus->capacity }}
                        </td>
                        <td class="px-6 py-4">
                            @if($schedule->status == 'active')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-600/20 text-green-500">Aktif</span>
                            @elseif($schedule->status == 'completed')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-600/20 text-blue-500">Selesai</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-600/20 text-gray-500">{{ ucfirst($schedule->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">Tidak ada jadwal hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">Pesanan Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Order ID</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Bus</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="border-b border-gray-700">
                       
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-400">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['chart_data']->pluck('date')) !!},
            datasets: [{
                label: 'Pendapatan',
                data: {!! json_encode($stats['chart_data']->pluck('revenue')) !!},
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                }
            }
        }
    });

    // Order Chart
    const orderCtx = document.getElementById('orderChart').getContext('2d');
    new Chart(orderCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stats['chart_data']->pluck('date')) !!},
            datasets: [{
                label: 'Pesanan',
                data: {!! json_encode($stats['chart_data']->pluck('orders')) !!},
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection