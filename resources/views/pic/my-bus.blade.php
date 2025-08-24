{{-- resources/views/pic/my-bus.blade.php --}}
@extends('pic.layout')

@section('content')
  <h1 class="text-2xl font-bold mb-6">My Bus</h1>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Informasi Bus</h2>
      <div id="bus-info" class="text-sm space-y-1">
        <div>Kode: <span id="bus-code" class="font-semibold">-</span></div>
        <div>Nama: <span id="bus-name" class="font-semibold">-</span></div>
        <div>Plat: <span id="bus-plate" class="font-semibold">-</span></div>
        <div>Kapasitas: <span id="bus-capacity" class="font-semibold">-</span></div>
        <div>Status: <span id="bus-status" class="font-semibold">-</span></div>
        <div>Driver: <span id="driver-name" class="font-semibold">-</span> (<span id="driver-phone">-</span>)</div>
      </div>
    </div>

    <div class="bg-white rounded shadow p-4 space-y-6">
      <div>
        <h2 class="font-semibold mb-3">Update Status Bus</h2>
        <div class="flex items-center gap-3">
          <select id="status-select" class="border rounded px-3 py-2">
            <option value="active">Active</option>
            <option value="maintenance">Maintenance</option>
            <option value="inactive">Inactive</option>
          </select>
          <button id="btn-update-status" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Update
          </button>
        </div>
      </div>

      <div>
        <h2 class="font-semibold mb-3">Laporkan Breakdown</h2>
        <textarea id="breakdown-notes" rows="3" class="w-full border rounded p-2" placeholder="Tuliskan kendala..."></textarea>
        <button id="btn-breakdown" class="mt-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
          Laporkan
        </button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
  const routes = {
    myBus: "{{ route('pic.dashboard.my-bus') }}",
    updateStatus: "{{ route('pic.bus.status.update') }}",
    breakdown: "{{ route('pic.bus.breakdown') }}",
  };

  // Load bus info
  try {
    const { data } = await axios.get(routes.myBus);
    const b = data?.bus || data; // fleksibel
    if (b) {
      document.getElementById('bus-code').textContent = b.bus_code || '-';
      document.getElementById('bus-name').textContent = b.bus_name || '-';
      document.getElementById('bus-plate').textContent = b.plate_number || '-';
      document.getElementById('bus-capacity').textContent = b.capacity ?? '-';
      document.getElementById('bus-status').textContent = (b.status || '-').toUpperCase();
      document.getElementById('driver-name').textContent = b.driver_name || '-';
      document.getElementById('driver-phone').textContent = b.driver_phone || '-';
      document.getElementById('status-select').value = b.status || 'active';
    }
  } catch (e) {
    showToast('Gagal memuat informasi bus', 'error');
  }

  // Update status
  document.getElementById('btn-update-status').addEventListener('click', async () => {
    try {
      const status = document.getElementById('status-select').value;
      await axios.post(routes.updateStatus, { status });
      showToast('Status bus berhasil diperbarui');
      document.getElementById('bus-status').textContent = status.toUpperCase();
    } catch (e) {
      showToast(e?.response?.data?.message || 'Gagal update status', 'error');
    }
  });

  // Breakdown
  document.getElementById('btn-breakdown').addEventListener('click', async () => {
    const notes = document.getElementById('breakdown-notes').value.trim();
    if (!notes) return showToast('Mohon isi keterangan breakdown', 'warn');
    try {
      await axios.post(routes.breakdown, { notes });
      showToast('Laporan breakdown dikirim');
      document.getElementById('breakdown-notes').value = '';
    } catch (e) {
      showToast(e?.response?.data?.message || 'Gagal kirim laporan', 'error');
    }
  });
});
</script>
@endpush