{{-- resources/views/pic/layout.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>PIC Dashboard - PT Medal Sekarwangi BUS</title>
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Leaflet --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

  {{-- Axios CDN (opsional, jika belum di-bundle di app.js) --}}
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>

  <style>
    .nav-active { @apply bg-red-700 text-white; }
  </style>
</head>
<body class="bg-gray-100 text-gray-900">
  @php
    $u = auth()->user();
    $bus = $u?->bus;
  @endphp

  <div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-72 bg-black text-gray-200 flex flex-col">
      <div class="px-6 py-6 border-b border-gray-800">
        <div class="flex items-center gap-3">
          <img src="{{ asset('logo.jpg') }}" class="w-10 h-10 object-contain rounded" alt="Logo">
          <div>
            <div class="font-bold text-white">PIC Dashboard</div>
            <div class="text-xs text-gray-400">PT Medal Sekarwangi BUS</div>
          </div>
        </div>
      </div>

      <nav class="flex-1 px-2 py-4 space-y-1">
        <a href="{{ route('pic.dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('pic.dashboard') ? 'nav-active' : '' }}">
          Dashboard
        </a>
        <a href="{{ route('pic.dashboard.my-bus') }}"
           class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('pic.dashboard.my-bus') ? 'nav-active' : '' }}">
          My Bus
        </a>
        <a href="{{ route('pic.tracking.index') }}"
           class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('pic.tracking.index') ? 'nav-active' : '' }}">
          Tracking
        </a>
        <a href="{{ route('pic.schedules') }}"
           class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('pic.schedules') ? 'nav-active' : '' }}">
          Schedules
        </a>
        <a href="{{ route('pic.passengers') }}"
           class="block px-3 py-2 rounded hover:bg-red-800 hover:text-white {{ request()->routeIs('pic.passengers') ? 'nav-active' : '' }}">
          Passengers
        </a>
      </nav>

      <div class="px-4 py-4 border-t border-gray-800">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded">Logout</button>
        </form>
      </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1">
      {{-- Topbar --}}
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
          <div class="text-sm text-gray-500">Logged in as</div>
          <div class="font-semibold">{{ $u?->name }} <span class="text-xs text-gray-500">({{ strtoupper($u?->role) }})</span></div>
        </div>
        <div class="text-right">
          @if($bus)
            <div class="text-sm text-gray-500">Assigned Bus</div>
            <div class="font-semibold">{{ $bus->bus_code }} — {{ $bus->bus_name }}</div>
            <div class="text-xs text-gray-500">Plate: {{ $bus->plate_number }} | Capacity: {{ $bus->capacity }}</div>
          @else
            <div class="text-sm text-gray-500">No bus assigned</div>
          @endif
        </div>
      </div>

      {{-- Alerts --}}
      <div id="global-alert" class="max-w-6xl mx-auto mt-4 px-4"></div>

      {{-- Content --}}
      <div class="max-w-6xl mx-auto p-6">
        @yield('content')
      </div>
    </main>
  </div>

  {{-- Global helpers --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Axios setup
      if (window.axios) {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
      }

      window.showToast = function(message, type = 'success') {
        const box = document.getElementById('global-alert');
        const color = type === 'success' ? 'green' : (type === 'warn' ? 'yellow' : 'red');
        box.innerHTML = `
          <div class="bg-${color}-50 border border-${color}-200 text-${color}-800 px-4 py-3 rounded mb-4">
            ${message}
          </div>`;
        setTimeout(() => box.innerHTML = '', 4000);
      }
    });
  </script>

  @stack('scripts')
</body>
</html>