{{-- resources/views/pic/passengers.blade.php --}}
@extends('pic.layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Passengers</h1>
    <div class="flex items-center gap-2">
      <button id="btn-list-all" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">Semua</button>
      <button id="btn-list-today" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Hari ini</button>
    </div>
  </div>

  <div class="bg-white rounded shadow overflow-hidden">
    <div class="p-4">
      <input id="search" type="text" class="border rounded px-3 py-2 w-full md:w-80" placeholder="Cari nama / order code...">
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <tr>
          <th class="px-4 py-3">Order</th>
          <th class="px-4 py-3">Nama</th>
          <th class="px-4 py-3">Seat</th>
          <th class="px-4 py-3">Tanggal</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody id="passenger-body" class="divide-y"></tbody>
    </table>
    <div id="empty-pass" class="p-6 text-center text-gray-500 hidden">Tidak ada data</div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const routes = {
    all: "{{ route('pic.passengers') }}",
    today: "{{ route('pic.passengers.today') }}",
    checkin: (orderId) => "{{ route('pic.passengers.checkin', ['order' => '__ID__']) }}".replace('__ID__', orderId),
  };

  let cache = [];

  async function load(url) {
    const body = document.getElementById('passenger-body');
    body.innerHTML = `<tr><td class="px-4 py-3" colspan="6">Loading...</td></tr>`;
    document.getElementById('empty-pass').classList.add('hidden');
    try {
      const { data } = await axios.get(url);
      cache = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
      render(cache);
    } catch (e) {
      body.innerHTML = '';
      document.getElementById('empty-pass').classList.remove('hidden');
      showToast('Gagal memuat penumpang', 'error');
    }
  }

  function render(list) {
    const body = document.getElementById('passenger-body');
    if (!list.length) {
      body.innerHTML = '';
      document.getElementById('empty-pass').classList.remove('hidden');
      return;
    }
    document.getElementById('empty-pass').classList.add('hidden');
    body.innerHTML = list.map(p => `
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 font-semibold">${p.order_code || p.id || '-'}</td>
        <td class="px-4 py-3">${p.passenger_name || p.user_name || '-'}</td>
        <td class="px-4 py-3">${p.seat_number || '-'}</td>
        <td class="px-4 py-3">${p.travel_date || p.date || '-'}</td>
        <td class="px-4 py-3">
          <span class="px-2 py-1 rounded text-xs ${p.status==='checked_in' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
            ${p.status || 'pending'}
          </span>
        </td>
        <td class="px-4 py-3 text-right">
          ${p.status==='checked_in' ? 
            '<span class="text-xs text-gray-500">Sudah check-in</span>' :
            `<button data-id="${p.id}" class="btn-checkin bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs">Check-in</button>`
          }
        </td>
      </tr>
    `).join('');

    // Bind check-in buttons
    document.querySelectorAll('.btn-checkin').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        if (!id) return;
        if (!confirm('Konfirmasi Check-in penumpang ini?')) return;
        try {
          await axios.post(routes.checkin(id));
          showToast('Check-in berhasil');
          // update status locally
          cache = cache.map(x => x.id == id ? ({ ...x, status: 'checked_in' }) : x);
          render(cache);
        } catch (err) {
          showToast(err?.response?.data?.message || 'Gagal check-in', 'error');
        }
      });
    });
  }

  // Search
  document.getElementById('search').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    const filtered = cache.filter(p => {
      const name = (p.passenger_name || p.user_name || '').toLowerCase();
      const code = (p.order_code || p.id || '').toString().toLowerCase();
      return name.includes(q) || code.includes(q);
    });
    render(filtered);
  });

  document.getElementById('btn-list-all').addEventListener('click', () => load(routes.all));
  document.getElementById('btn-list-today').addEventListener('click', () => load(routes.today));

  // default
  load(routes.today);
});
</script>
@endpush