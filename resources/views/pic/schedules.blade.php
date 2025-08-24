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
    all: "{{ route('pic.schedules') }}",
    today: "{{ route('pic.schedules.today') }}",
  };

  async function load(url) {
    const body = document.getElementById('schedule-body');
    body.innerHTML = `<tr><td class="px-4 py-3" colspan="6">Loading...</td></tr>`;
    document.getElementById('empty').classList.add('hidden');
    try {
      const { data } = await axios.get(url);
      const list = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
      if (!list.length) {
        body.innerHTML = '';
        document.getElementById('empty').classList.remove('hidden');
        return;
      }
      body.innerHTML = list.map(s => `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 font-semibold">${s.route_from} → ${s.route_to}</td>
          <td class="px-4 py-3">${s.departure_time}</td>
          <td class="px-4 py-3">${s.arrival_time}</td>
          <td class="px-4 py-3">Rp ${Number(s.price || 0).toLocaleString('id-ID')}</td>
          <td class="px-4 py-3 text-xs">${Array.isArray(s.days_of_week) ? s.days_of_week.join(', ') : (s.days_of_week || '-')}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-xs ${s.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
              ${s.is_active ? 'Active' : 'Inactive'}
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
  load(routes.today);
});
</script>
@endpush