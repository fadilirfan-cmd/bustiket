<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Admin Dashboard - PT Medal Sekarwangi BUS</title>
    
    @vite(['resources/css/app.css','resources/js/app.js'])
    
    {{-- Additional CSS Libraries --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
    {{-- Scripts --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    
    <style>
        .nav-active { @apply bg-red-700 text-white; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-900">
    @php
        $u = auth()->user();
    @endphp
    
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-72 bg-black text-gray-200 flex flex-col">
            <div class="px-6 py-6 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.jpg') }}" class="w-10 h-10 object-contain rounded" alt="Logo">
                    <div>
                        <div class="font-bold text-white">Admin Dashboard</div>
                        <div class="text-xs text-gray-400">PT Medal Sekarwangi BUS</div>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </div>
                </a>
                
                {{-- Fleet Management Section --}}
                <div class="pt-4 pb-2">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase">Fleet Management</div>
                </div>
                
                {{-- Buses --}}
                <a href="#"
                   class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('admin.buses.*') ? 'nav-active' : '' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <span>Buses</span>
                    </div>
                </a>
                
                {{-- Schedules --}}
                <a href="{{ route('admin.schedules.index') }}"
                   class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('admin.schedules.*') ? 'nav-active' : '' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Schedules</span>
                    </div>
                </a>
                
                {{-- Live Tracking --}}
                <a href="{{ route('admin.tracking.index') }}"
                   class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('admin.tracking.*') ? 'nav-active' : '' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Live Tracking</span>
                    </div>
                </a>
                
                {{-- Order Management Section --}}
                <div class="pt-4 pb-2">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase">Order Management</div>
                </div>
                
                {{-- Orders --}}
                <a href="{{ route('admin.orders.index') }}"
                   class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('admin.orders.*') ? 'nav-active' : '' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span>Orders</span>
                        @php
                            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                        @endphp
                        @if($pendingOrders > 0)
                            <span class="ml-auto bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
                        @endif
                    </div>
                </a>
                
                {{-- Export & Reports --}}
                <div class="ml-4 space-y-1">
                    <a href="{{ route('admin.orders.export.excel') }}"
                       class="block px-3 py-1 text-sm rounded hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export Excel</span>
                        </div>
                    </a>
                    <a href="{{ route('admin.orders.export.pdf') }}"
                       class="block px-3 py-1 text-sm rounded hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span>Export PDF</span>
                        </div>
                    </a>
                </div>
            </nav>
            
            {{-- Bottom Section --}}
            <div class="px-4 py-4 border-t border-gray-800">
                <div class="mb-3">
                    <div class="text-xs text-gray-500">Logged in as</div>
                    <div class="text-sm font-semibold text-white">{{ $u?->name }}</div>
                    <div class="text-xs text-gray-400">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded">
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        
        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden">
            {{-- Topbar --}}
            <div class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                        <nav class="text-sm text-gray-600 mt-1">
                            @yield('breadcrumb')
                        </nav>
                    </div>
                    <div class="flex items-center gap-4">
                        {{-- Quick Stats (Loaded via AJAX) --}}
                        <div id="quick-stats" class="flex items-center gap-6 text-sm">
                            <div>
                                <span class="text-gray-500">Active Buses:</span>
                                <span class="font-semibold text-gray-800 ml-1" id="active-buses-count">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Today's Schedules:</span>
                                <span class="font-semibold text-gray-800 ml-1" id="today-schedules-count">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Pending Orders:</span>
                                <span class="font-semibold text-gray-800 ml-1" id="pending-orders-count">-</span>
                            </div>
                        </div>
                        
                        {{-- Quick Actions --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.buses.create') }}" 
                               class="p-2 text-gray-600 hover:text-gray-800" 
                               title="Add New Bus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </a>
                            
                            {{-- Refresh Stats --}}
                            <button onclick="loadQuickStats()" 
                                    class="p-2 text-gray-600 hover:text-gray-800" 
                                    title="Refresh Stats">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Alerts --}}
            <div id="global-alert" class="max-w-7xl mx-auto mt-4 px-4">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-4 flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-4">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4 flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-4">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-4 flex items-center justify-between">
                        <span>{{ session('warning') }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-4">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
            
            {{-- Main Content Area --}}
            <div class="max-w-7xl mx-auto p-6">
                @yield('content')
            </div>
        </main>
    </div>
    
    {{-- Global JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Axios setup
            if (window.axios) {
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
            }
            
            // Load quick stats
            loadQuickStats();
            
            // Refresh stats every 30 seconds
            setInterval(loadQuickStats, 30000);
            
            // Global toast notification
            window.showToast = function(message, type = 'success') {
                const box = document.getElementById('global-alert');
                const colorMap = {
                    'success': 'green',
                    'error': 'red',
                    'warning': 'yellow',
                    'info': 'blue'
                };
                const color = colorMap[type] || 'gray';
                
                const alertDiv = document.createElement('div');
                alertDiv.className = `bg-${color}-50 border border-${color}-200 text-${color}-800 px-4 py-3 rounded mb-4 flex items-center justify-between`;
                alertDiv.innerHTML = `
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-4">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                `;
                
                box.appendChild(alertDiv);
                
                setTimeout(() => {
                    if (alertDiv && alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('#global-alert > div');
            alerts.forEach(alert => {
                setTimeout(() => alert.remove(), 5000);
            });
        });
        
        // Load Quick Stats function
        function loadQuickStats() {
            if (window.axios) {
                axios.get('{{ route('admin.dashboard.stats') }}')
                    .then(response => {
                        const data = response.data;
                        
                        // Update counts
                        document.getElementById('active-buses-count').textContent = data.activeBuses || 0;
                        document.getElementById('today-schedules-count').textContent = data.todaySchedules || 0;
                        document.getElementById('pending-orders-count').textContent = data.pendingOrders || 0;
                        
                        // Update pending orders badge if exists
                        const ordersBadge = document.querySelector('a[href="{{ route('admin.orders.index') }}"] .bg-red-600');
                        if (ordersBadge && data.pendingOrders > 0) {
                            ordersBadge.textContent = data.pendingOrders;
                        }
                    })
                    .catch(error => {
                        console.error('Failed to load stats:', error);
                    });
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>