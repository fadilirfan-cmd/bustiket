{{-- resources/views/pic/my-bus.blade.php --}}
@extends('pic.layout')

@section('content')
  <h1 class="text-2xl font-bold mb-6">My Bus</h1>

  <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
    <div class="bg-white rounded shadow p-4">
      <h2 class="font-semibold mb-3">Informasi Bus</h2>
      <div id="bus-info" class="text-sm space-y-1">
        <div>Nomor Bus  : <span id="bus-code" class="font-semibold">-</span></div>
        <div>Nama Bus: <span id="bus-name" class="font-semibold">-</span></div>
        <div>Kapasitas Bus: <span id="bus-capacity" class="font-semibold">-</span></div>
        <div>Type Bus: <span id="bus-status" class="font-semibold">-</span></div>
      </div>
    </div>

    
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
  const routes = {
    myBus: "{{ route('pic.dashboard.my-bus') }}"
  };

  // Load bus info
  try {
    const { data } = await axios.get(routes.myBus);
    console.log(data);
    const b = data?.bus || data; // fleksibel
    if (b) {
      document.getElementById('bus-code').textContent = b.bus.bus_number || '-';
      document.getElementById('bus-name').textContent = b.bus.bus_name || '-';
      document.getElementById('bus-capacity').textContent = b.bus.capacity ?? '-';
      document.getElementById('bus-status').textContent = (b.bus.type || '-').toUpperCase();
      
    }
  } catch (e) {
    showToast('Gagal memuat informasi bus', 'error');
  }

  
});
</script>
@endpush