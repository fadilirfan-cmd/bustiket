{{-- resources/views/pic/tracking.blade.php --}}
@extends('pic.layout')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Tracking</h1>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
      <div id="map" class="w-full h-[520px] rounded shadow bg-gray-200"></div>

      <div class="bg-white rounded shadow p-4 flex flex-wrap gap-3 items-center">
        <button id="btn-start-trip" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          Start Trip
        </button>
        <button id="btn-end-trip" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
          End Trip
        </button>
        <div class="ml-auto flex items-center gap-2">
          <button id="btn-use-gps" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Kirim Lokasi (GPS)
          </button>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" id="auto-refresh" class="rounded border-gray-300">
            Auto refresh 10s
          </label>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Current Trip</h2>
        <div class="text-sm space-y-1">
          <div>Status: <span id="trip-status" class="font-semibold">-</span></div>
          <div>Started: <span id="trip-started">-</span></div>
          <div>Last Loc: <span id="trip-lastloc">-</span></div>
          <div>Updated: <span id="trip-updated">-</span></div>
        </div>
      </div>

      <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Manual Update</h2>
        <div class="grid grid-cols-2 gap-3">
          <input id="lat" type="text" class="border rounded px-3 py-2" placeholder="Latitude">
          <input id="lng" type="text" class="border rounded px-3 py-2" placeholder="Longitude">
        </div>
        <button id="btn-send-manual" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          Kirim Lokasi Manual
        </button>
        <div class="text-xs text-gray-500 mt-2">Akurasi/Kecepatan akan diisi otomatis jika dari GPS.</div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const routes = {
    currentTrip: "{{ route('pic.tracking.current') }}",
    update: "{{ route('pic.tracking.update') }}",
    start: "{{ route('pic.tracking.start') }}",
    end: "{{ route('pic.tracking.end') }}",
  };

  // Leaflet Map
  let map = L.map('map').setView([-6.2, 106.8], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  let busMarker = null;
  let routeLine = L.polyline([], { color: 'red', weight: 4 }).addTo(map);

  function setBusMarker(lat, lng) {
    const latLng = [lat, lng];
    if (!busMarker) {
      busMarker = L.marker(latLng, { title: 'Bus' }).addTo(map);
    } else {
      busMarker.setLatLng(latLng);
    }
  }

  async function loadCurrentTrip(fit = false) {
    try {
      const { data } = await axios.get(routes.currentTrip);

      // status + info
      const status = data?.status || data?.trip?.status || 'Unknown';
      document.getElementById('trip-status').textContent = status.toUpperCase();
      document.getElementById('trip-started').textContent = data?.started_at ? new Date(data.started_at).toLocaleString() : (data?.trip?.started_at ? new Date(data.trip.started_at).toLocaleString() : '-');

      const last = data?.latest_location || data?.location;
      if (last?.lat && last?.lng) {
        setBusMarker(last.lat, last.lng);
        document.getElementById('trip-lastloc').textContent = `${Number(last.lat).toFixed(5)}, ${Number(last.lng).toFixed(5)}`;
        document.getElementById('trip-updated').textContent = last.updated_at ? new Date(last.updated_at).toLocaleString() : '-';
      }

      // polyline
      const points = data?.points || data?.history || [];
      if (Array.isArray(points) && points.length) {
        const latlngs = points.map(p => [Number(p.lat), Number(p.lng)]);
        routeLine.setLatLngs(latlngs);
        if (fit) {
          const bounds = L.latLngBounds(latlngs);
          map.fitBounds(bounds.pad(0.25));
        }
      } else if (last?.lat && last?.lng && fit) {
        map.setView([last.lat, last.lng], 14);
      }
    } catch (e) {
      // silent, to avoid noise
    }
  }

  // First load
  loadCurrentTrip(true);

  // Auto refresh
  let interval = null;
  const autoCb = document.getElementById('auto-refresh');
  autoCb.addEventListener('change', () => {
    if (autoCb.checked) {
      interval = setInterval(() => loadCurrentTrip(false), 10000);
    } else if (interval) {
      clearInterval(interval);
    }
  });

  // Start/End trip
  document.getElementById('btn-start-trip').addEventListener('click', async () => {
    try {
      await axios.post(routes.start);
      showToast('Trip dimulai');
      loadCurrentTrip(true);
    } catch (e) {
      showToast(e?.response?.data?.message || 'Gagal mulai trip', 'error');
    }
  });

  document.getElementById('btn-end-trip').addEventListener('click', async () => {
    try {
      await axios.post(routes.end);
      showToast('Trip diakhiri');
      loadCurrentTrip(true);
    } catch (e) {
      showToast(e?.response?.data?.message || 'Gagal akhiri trip', 'error');
    }
  });

  // GPS update
  document.getElementById('btn-use-gps').addEventListener('click', () => {
    if (!navigator.geolocation) {
      return showToast('Browser tidak mendukung Geolocation', 'error');
    }
    navigator.geolocation.getCurrentPosition(async pos => {
      const { latitude, longitude, accuracy, speed } = pos.coords;
      try {
        await axios.post(routes.update, {
          lat: latitude, lng: longitude,
          accuracy: accuracy ?? null, speed: speed ?? null
        });
        showToast('Lokasi terkirim');
        setBusMarker(latitude, longitude);
        routeLine.addLatLng([latitude, longitude]);
        map.setView([latitude, longitude], 15);
        loadCurrentTrip(false);
      } catch (e) {
        showToast(e?.response?.data?.message || 'Gagal mengirim lokasi', 'error');
      }
    }, err => {
      showToast('Gagal mengambil lokasi: ' + err.message, 'error');
    }, { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 });
  });

  // Manual update
  document.getElementById('btn-send-manual').addEventListener('click', async () => {
    const lat = parseFloat(document.getElementById('lat').value);
    const lng = parseFloat(document.getElementById('lng').value);
    if (isNaN(lat) || isNaN(lng)) return showToast('Masukkan lat/lng valid', 'warn');
    try {
      await axios.post(routes.update, { lat, lng });
      showToast('Lokasi manual terkirim');
      setBusMarker(lat, lng);
      routeLine.addLatLng([lat, lng]);
      map.setView([lat, lng], 15);
      loadCurrentTrip(false);
    } catch (e) {
      showToast(e?.response?.data?.message || 'Gagal mengirim lokasi', 'error');
    }
  });
});
</script>
@endpush