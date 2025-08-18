<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Medal Sekarwangi – Beranda</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-white text-gray-800">

<!-- ===== HEADER ===== -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-10">
            <span class="text-xl font-bold text-red-600">Medal <span class="text-gray-800">Sekarwangi</span></span>
        </div>
        <nav class="space-x-4">
            <a href="{{ url('/') }}" class="text-gray-700 hover:text-red-600">Beranda</a>
            <a href="#jadwal" class="text-gray-700 hover:text-red-600">Jadwal</a>
            @auth
                <a href="{{ route('bookings.history') }}" class="text-red-600 font-medium">Riwayat</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-700 hover:text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-red-600 text-white px-4 py-2 rounded">Login</a>
            @endauth
        </nav>
    </div>
</header>

<!-- ===== HERO SECTION ===== -->
<section class="relative h-[62vh] flex items-center justify-center text-white">
    <img src="{{ asset('hero.png') }}" alt="Hero" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative text-center">
        <img src="{{ asset('logo.jpg') }}" class="w-24 h-24 mx-auto mb-4">
        <h1 class="text-5xl font-extrabold drop-shadow-lg">BUS Medal <span class="text-red-500">Sekarwangi</span></h1>
        <p class="mt-4 text-xl drop-shadow-sm">Pesan tiket bus & pantau posisi secara real-time</p>
        <a href="#jadwal" class="mt-6 inline-block bg-red-600 hover:bg-red-700 px-8 py-3 rounded-full font-semibold">Lihat Jadwal</a>
    </div>
</section>

<!-- ===== JADWAL SECTION ===== -->
<section id="jadwal" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Jadwal Keberangkatan</h2>

        <form id="filterForm" class="mb-8 flex flex-wrap gap-4 justify-center">
            <select name="route" id="routeFilter" class="border border-gray-300 rounded px-4 py-2">
                <option value="">Semua Rute</option>
                @foreach($routes as $r)
                    <option value="{{ $r->id }}">{{ $r->origin }} → {{ $r->destination }}</option>
                @endforeach
            </select>
            <input type="date" name="date" id="dateFilter" class="border border-gray-300 rounded px-4 py-2">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">Cari</button>
        </form>

        <!-- Container card -->
        <div id="scheduleList" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- diisi AJAX -->
        </div>
    </div>
</section>

<!-- ===== MODAL TRACKING FULL-SCREEN ===== -->
<div id="trackingModal" class="fixed inset-0 bg-black/70 z-50 hidden flex items-center justify-center p-2">
    <div class="relative w-full h-full max-w-full max-h-full md:max-w-5xl md:max-h-[90vh] bg-white rounded overflow-hidden">
        <!-- tombol tutup -->
        <button onclick="closeTracking()"
                class="absolute top-2 right-2 md:top-4 md:right-4 z-20 bg-white/90 hover:bg-gray-200 text-gray-800 rounded-full p-2 shadow">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- map -->
        <div id="trackingMap" class="w-full h-full"></div>

        <!-- info bus -->
        <div id="busInfo" class="absolute bottom-2 left-2 md:bottom-4 md:left-4 bg-white/90 px-3 py-1 rounded shadow text-xs md:text-sm">
            <span id="busName">Memuat...</span>
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="bg-gray-800 text-white py-6">
    <div class="container mx-auto text-center">
        &copy; {{ date('Y') }} Medal Sekarwangi. All rights reserved.
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault(); // mencegah reload
    loadSchedules();
});
loadSchedules(); // load awal

async function loadSchedules() {
    const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
    const res = await fetch(`/api/schedules?${params}`);
    const data = await res.json();
    const list = Array.isArray(data) ? data : data.data || [];

    const container = document.getElementById('scheduleList');
    container.innerHTML = '';

    if (!list.length) {
        container.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500 text-lg">Tidak ada jadwal yang cocok.</p></div>';
        return;
    }

    list.forEach(s => {
        const card = `
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
                <div class="p-6">
                    <h3 class="text-xl font-bold">${s.bus.bus_name}</h3>
                    <p class="text-sm text-gray-500 mb-2">${s.bus.type}</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Rute:</span><span class="font-medium">${s.route.origin} → ${s.route.destination}</span></div>
                        <div class="flex justify-between"><span>Berangkat:</span><span class="font-medium">${s.departure_time.substring(0,16)}</span></div>
                        <div class="flex justify-between"><span>Kursi tersedia:</span><span class="text-green-600 font-bold">${s.available_seats}/${s.bus.capacity}</span></div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="/schedules/${s.id}/order" class="flex-1 bg-red-600 text-white text-center py-2 rounded">Pesan Tiket</a>
                        <button onclick="openTracking(${s.bus.id})" class="bg-blue-600 text-white px-3 py-2 rounded">Tracking 🗺️</button>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', card);
    });
}

/* === TRACKING MODAL === */
let map, marker;
function openTracking(busId) {
    document.getElementById('trackingModal').classList.remove('hidden');
    if (!map) {
        map = L.map('trackingMap').setView([-6.2, 106.8166], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    }
    fetch(`/api/bus-location/${busId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('busName').textContent = data.name;
            map.setView([data.lat, data.lng], 14);
            if (marker) map.removeLayer(marker);
            marker = L.marker([data.lat, data.lng]).addTo(map).bindPopup(`<b>${data.name}</b>`).openPopup();
        })
        .catch(() => {
            const lat = -6.2 + Math.random() * 0.02;
            const lng = 106.8166 + Math.random() * 0.02;
            map.setView([lat, lng], 14);
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map).bindPopup(`Bus #${busId}`).openPopup();
            document.getElementById('busName').textContent = `Bus #${busId}`;
        });
}
function closeTracking() {
    document.getElementById('trackingModal').classList.add('hidden');
}
</script>
<script>
    // Scroll smooth untuk link internal
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
<script>
/* === SET TANGGAL HARI INI OTOMATIS === */
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().slice(0, 10);
    document.getElementById('dateFilter').value = today;
});
</script>
</body>
</html>