{{-- resources/views/pic/dashboard.blade.php --}}
@extends('pic.layout')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
      <div class="text-sm text-gray-500">Trip Status</div>
      <div id="stat-trip-status" class="text-xl font-semibold">-</div>
    </div>
    <div class="bg-white rounded shadow p-4">
      <div class="text-sm text-gray-500">Last Location</div>
      <div id="stat-last-location" class="text-xl font-semibold">-</div>
      <div id="stat-last-updated" class="text-xs text-gray-500"></div>
    </div>
    <div class="bg-white rounded shadow p-4">
      <div class="text-sm text-gray-500">Today's Schedules</div>
      <div id="stat-today-schedules" class="text-xl font-semibold">0</div>
    </div>
    <div class="bg-white rounded shadow p-4">
      <div class="text-sm text-gray-500">Passengers Today</div>
      <div id="stat-passengers-today" class="text-xl font-semibold">0</div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold">Today's Schedules</h2>
        <a href="{{ route('pic.schedules.today') }}" class="text-sm text-red-600 hover:underline">Lihat semua</a>
      </div>
      <div id="today-schedule-list" class="space-y-3 text-sm"></div>
    </div>

    <div class="bg-white rounded shadow p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold">Passengers Today</h2>
        <a href="{{ route('pic.passengers') }}" class="text-sm text-red-600 hover:underline">Lihat semua</a>
      </div>
      <div id="today-passenger-list" class="space-y-3 text-sm max-h-64 overflow-auto"></div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
  const routes = {
    currentTrip: "{{ route('pic.tracking.current') }}",
    todaySchedules: "{{ route('pic.schedules.today') }}",
    todayPassengers: "{{ route('pic.passengers.today') }}",
    myBus: "{{ route('pic.dashboard.my-bus') }}",
  };

  // Load current trip
  try {
    const { data } = await axios.get(routes.currentTrip);
    const status = data?.status || (data?.trip?.status) || 'Unknown';
    document.getElementById('stat-trip-status').textContent = status.toString().toUpperCase();

    const last = data?.latest_location || data?.location || null;
    if (last?.lat && last?.lng) {
      document.getElementById('stat-last-location').textContent = `${Number(last.lat).toFixed(5)}, ${Number(last.lng).toFixed(5)}`;
      if (last.updated_at) document.getElementById('stat-last-updated').textContent = `Updated: ${new Date(last.updated_at).toLocaleString()}`;
    } else {
      document.getElementById('stat-last-location').textContent = '-';
    }
  } catch (e) {
    showToast('Gagal mengambil status trip', 'error');
  }

  // Load today's schedules
  try {
    const { data } = await axios.get(routes.todaySchedules);
    const list = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
    document.getElementById('stat-today-schedules').textContent = list.length;

    const wrap = document.getElementById('today-schedule-list');
    wrap.innerHTML = list.slice(0,5).map(s => `
      <div class="p-3 border rounded">
        <div class="font-semibold">${s.route_from} → ${s.route_to}</div>
        <div class="text-xs text-gray-500">Dep: ${s.departure_time} | Arr: ${s.arrival_time}</div>
      </div>
    `).join('') || '<div class="text-gray-500">Tidak ada jadwal hari ini</div>';
  } catch (e) {
    showToast('Gagal mengambil jadwal hari ini', 'error');
  }

  // Load today's passengers
  try {
    const { data } = await axios.get(routes.todayPassengers);
    const list = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
    document.getElementById('stat-passengers-today').textContent = list.length;

    const wrap = document.getElementById('today-passenger-list');
    wrap.innerHTML = list.slice(0,8).map(p => `
      <div class="p-3 border rounded flex items-center justify-between">
        <div>
          <div class="font-semibold">${p.passenger_name || p.user_name || '-'}</div>
          <div class="text-xs text-gray-500">Seat: ${p.seat_number || '-'}</div>
        </div>
        <div class="text-xs ${p.status==='checked_in'?'text-green-600':'text-yellow-600'}">${p.status || 'pending'}</div>
      </div>
    `).join('') || '<div class="text-gray-500">Tidak ada penumpang hari ini</div>';
  } catch (e) {
    showToast('Gagal mengambil data penumpang hari ini', 'error');
  }
});
</script>
@endpush