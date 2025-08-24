@extends('pic.layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Booking Orders</h1>
    <button id="btn-load" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
      Muat Ulang
    </button>
  </div>

  <div class="bg-white rounded shadow overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-left">
        <tr>
          <th class="px-4 py-3">Order Number</th>
          <th class="px-4 py-3">Nama Bus</th>
          <th class="px-4 py-3">Passenger</th>
          <th class="px-4 py-3">Phone</th>
          <th class="px-4 py-3">Jemput</th>
          <th class="px-4 py-3">Seat</th>
          <th class="px-4 py-3">Tanggal Order</th>
        </tr>
      </thead>
      <tbody id="booking-body" class="divide-y"></tbody>
    </table>
    <div id="empty" class="p-6 text-center text-gray-500 hidden">Tidak ada data</div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const url = "{{ route('pic.order') }}";

  async function load() {
    const body = document.getElementById('booking-body');
    body.innerHTML = `<tr><td class="px-4 py-3" colspan="7">Loading...</td></tr>`;
    document.getElementById('empty').classList.add('hidden');

    try {
      const response = await axios.get(url);
      const orders = response.data?.schedules?.data || [];
      console.log("Orders path:", response.data?.schedules?.data);

      if (!orders.length) {
        body.innerHTML = '';
        document.getElementById('empty').classList.remove('hidden');
        return;
      }

      body.innerHTML = orders.map(o => `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 font-semibold">${o.order_number}</td>
          <td class="px-4 py-3">${o.schedule?.bus?.bus_name || 'N/A'}</td>
          <td class="px-4 py-3">${o.passenger_name}</td>
          <td class="px-4 py-3">${o.passenger_phone || '-'}</td>
          <td class="px-4 py-3">${o.jemput || '-'}</td>
          <td class="px-4 py-3">${o.seat_numbers || '-'}</td>
          <td class="px-4 py-3">
            ${new Date(o.created_at).toLocaleString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
              timeZone: 'Asia/Jakarta',
              hour12: false
            }).replace(/\./g, ':')} WIB
          </td>
        </tr>
      `).join('');
    } catch (e) {
      console.error("Error loading orders:", e);
      body.innerHTML = '';
      document.getElementById('empty').classList.remove('hidden');
      alert('Gagal memuat data order');
    }
  }

  document.getElementById('btn-load').addEventListener('click', load);
  load();
});
</script>
@endpush