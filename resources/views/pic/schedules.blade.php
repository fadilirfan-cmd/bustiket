{{-- resources/views/pic/schedules.blade.php --}}
@extends('pic.layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Schedules</h1>
    <div class="flex items-center gap-2">
      <button id="btn-load-all" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">Semua</button>
      <button id="btn-load-today" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Hari ini</button>
    </div>
  </div>

  <div class="bg-white rounded shadow overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <tr>
        <th class="px-4 py-3">Nama Bus</th>  
        <th class="px-4 py-3">Rute</th>
          <th class="px-4 py-3">Berangkat</th>
          <th class="px-4 py-3">Tiba</th>
          <th class="px-4 py-3">Harga</th>
          <th class="px-4 py-3">Hari</th>
          <th class="px-4 py-3">Status</th>
        </tr>
      </thead>
      <tbody id="schedule-body" class="divide-y"></tbody>
    </table>
    <div id="empty" class="p-6 text-center text-gray-500 hidden">Tidak ada data</div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const routes = {
    all: "{{ route('pic.schedules') }}"
  };

  async function load(url) {
    const body = document.getElementById('schedule-body');
    body.innerHTML = `<tr><td class="px-4 py-3" colspan="6">Loading...</td></tr>`;
    document.getElementById('empty').classList.add('hidden');
    try {
      const response = await axios.get(url);
      console.log(response.data);
const schedules = response.data.schedules || response.data;
const list = Array.isArray(schedules.data) ? schedules.data : (Array.isArray(schedules) ? schedules : []);
      if (!list.length) {
        body.innerHTML = '';
        document.getElementById('empty').classList.remove('hidden');
        return;
      }
      body.innerHTML = list.map(s => `
        <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 font-semibold">${s.bus.bus_name}</td>
          <td class="px-4 py-3 font-semibold">${s.route.origin} → ${s.route.destination}</td>
          <td class="px-4 py-3">${new Date(s.departure_time).toLocaleString('id-ID', {
  day: '2-digit',
  month: 'long',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit',
  timeZone: 'Asia/Jakarta',
  hour12: false
}).replace(/\./g, ':')} WIB</td>
          <td class="px-4 py-3">${new Date(s.arrival_time).toLocaleString('id-ID', {
  day: '2-digit',
  month: 'long',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit',
  timeZone: 'Asia/Jakarta',
  hour12: false
}).replace(/\./g, ':')} WIB</td>
          <td class="px-4 py-3">Rp ${Number(s.price || 0).toLocaleString('id-ID')}</td>
         <td class="px-4 py-3">${new Date(s.departure_time).toLocaleDateString('id-ID', {
  weekday: 'long',
  timeZone: 'Asia/Jakarta'
})}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-xs ${s.status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
              ${s.status ? 'Active' : 'Inactive'}
            </span>
          </td>
        </tr>
      `).join('');
    } catch (e) {
      body.innerHTML = '';
      document.getElementById('empty').classList.remove('hidden');
      showToast('Gagal memuat jadwal', 'error');
    }
  }

  document.getElementById('btn-load-all').addEventListener('click', () => load(routes.all));
  document.getElementById('btn-load-today').addEventListener('click', () => load(routes.today));
  load(routes.all);
});
</script>
@endpush