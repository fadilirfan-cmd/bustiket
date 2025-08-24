<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GPS Tracker - Bus {{ $bus->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pulse-btn {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-red-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-xl font-bold">Bus Location Tracker</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Bus {{ $bus->bus_name ?? 'ID: '.$bus->id }}</h1>
                    <p class="text-gray-600 mb-6">{{ $bus->type ?? 'Bus Reguler' }}</p>

                    <div id="locationInfo" class="mb-6 p-4 bg-gray-50 rounded-lg hidden">
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Lokasi Saat Ini</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Latitude</p>
                                <p class="font-medium" id="latDisplay">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Longitude</p>
                                <p class="font-medium" id="lngDisplay">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Akurasi</p>
                                <p class="font-medium" id="accuracyDisplay">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kecepatan</p>
                                <p class="font-medium" id="speedDisplay">-</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Terakhir Diperbarui</p>
                                <p class="font-medium" id="timestampDisplay">-</p>
                            </div>
                        </div>
                    </div>

                    <div id="mapContainer" class="h-64 bg-gray-200 rounded-lg mb-6 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <p class="text-gray-500">Peta akan muncul setelah lokasi terdeteksi</p>
                        </div>
                        <div id="map" class="h-full w-full rounded-lg"></div>
                    </div>

                    <div class="space-y-4">
                        <button id="startTracking" class="w-full bg-red-600 text-white py-4 rounded-lg font-bold pulse-btn flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Mulai Pelacakan
                        </button>
                        
                        <button id="stopTracking" class="w-full bg-gray-600 text-white py-4 rounded-lg font-bold hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Hentikan Pelacakan
                        </button>
                    </div>

                    <div id="statusMessages" class="mt-6 space-y-2">
                        <!-- Status messages will appear here -->
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-800 text-white py-4">
            <div class="container mx-auto px-4 text-center text-sm">
                &copy; {{ date('Y') }} Medal Sekarwangi. Aplikasi GPS Tracking untuk Bus.
            </div>
        </footer>
    </div>

    <!-- Modal untuk notifikasi -->
    <div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div id="notificationContent" class="text-center mb-6">
                <!-- Content will be dynamically inserted -->
            </div>
            <button id="closeNotification" class="w-full bg-red-600 text-white py-2 rounded-md font-medium">
                Tutup
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const busId = 4;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            let watchId = null;
            let trackingActive = false;
            
            const startTrackingBtn = document.getElementById('startTracking');
            const stopTrackingBtn = document.getElementById('stopTracking');
            const locationInfo = document.getElementById('locationInfo');
            const latDisplay = document.getElementById('latDisplay');
            const lngDisplay = document.getElementById('lngDisplay');
            const accuracyDisplay = document.getElementById('accuracyDisplay');
            const speedDisplay = document.getElementById('speedDisplay');
            const timestampDisplay = document.getElementById('timestampDisplay');
            const statusMessages = document.getElementById('statusMessages');
            const notificationModal = document.getElementById('notificationModal');
            const notificationContent = document.getElementById('notificationContent');
            const closeNotification = document.getElementById('closeNotification');
            
            // Check if geolocation is available
            if (!navigator.geolocation) {
                showNotification('Error', 'Geolocation tidak didukung oleh browser Anda. Silakan gunakan browser lain.');
                startTrackingBtn.disabled = true;
                return;
            }
            
            // Start tracking
            startTrackingBtn.addEventListener('click', function() {
                if (trackingActive) return;
                
                addStatusMessage('Memulai pelacakan lokasi...');
                
                startTrackingBtn.classList.remove('pulse-btn');
                startTrackingBtn.classList.add('bg-gray-400');
                startTrackingBtn.textContent = 'Mendeteksi lokasi...';
                
                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };
                
                try {
                    watchId = navigator.geolocation.watchPosition(handlePosition, handleError, options);
                    trackingActive = true;
                    
                    // Show stop button after 2 seconds (giving time for first position)
                    setTimeout(() => {
                        startTrackingBtn.classList.add('hidden');
                        stopTrackingBtn.classList.remove('hidden');
                    }, 2000);
                    
                } catch (error) {
                    addStatusMessage('Error: ' + error.message, true);
                    startTrackingBtn.classList.remove('bg-gray-400');
                    startTrackingBtn.classList.add('pulse-btn');
                    startTrackingBtn.textContent = 'Mulai Pelacakan';
                }
            });
            
            // Stop tracking
            stopTrackingBtn.addEventListener('click', function() {
                if (!trackingActive) return;
                
                navigator.geolocation.clearWatch(watchId);
                trackingActive = false;
                watchId = null;
                
                addStatusMessage('Pelacakan dihentikan.');
                
                stopTrackingBtn.classList.add('hidden');
                startTrackingBtn.classList.remove('hidden', 'bg-gray-400');
                startTrackingBtn.classList.add('pulse-btn');
                startTrackingBtn.textContent = 'Mulai Pelacakan';
            });
            
            // Close notification
            closeNotification.addEventListener('click', function() {
                notificationModal.classList.add('hidden');
            });
            
            // Handle position update
            function handlePosition(position) {
                const { latitude, longitude, accuracy } = position.coords;
                const speed = position.coords.speed || 0;
                const timestamp = new Date(position.timestamp);
                
                // Update display
                locationInfo.classList.remove('hidden');
                latDisplay.textContent = latitude.toFixed(6);
                lngDisplay.textContent = longitude.toFixed(6);
                accuracyDisplay.textContent = accuracy.toFixed(1) + ' meter';
                speedDisplay.textContent = (speed * 3.6).toFixed(1) + ' km/h'; // Convert m/s to km/h
                timestampDisplay.textContent = formatDateTime(timestamp);
                
                // Send to server
                sendLocationToServer({
                    latitude,
                    longitude,
                    accuracy,
                    speed,
                    timestamp: timestamp.toISOString()
                });
            }
            
            // Handle geolocation errors
            function handleError(error) {
                let message;
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'Akses lokasi ditolak. Silakan aktifkan GPS dan izinkan akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
                        break;
                    case error.TIMEOUT:
                        message = 'Waktu permintaan lokasi habis. Coba lagi.';
                        break;
                    default:
                        message = 'Terjadi kesalahan yang tidak diketahui.';
                        break;
                }
                
                addStatusMessage('Error: ' + message, true);
                showNotification('Error', message);
                
                // Reset tracking
                if (trackingActive) {
                    navigator.geolocation.clearWatch(watchId);
                    trackingActive = false;
                    watchId = null;
                }
                
                stopTrackingBtn.classList.add('hidden');
                startTrackingBtn.classList.remove('hidden', 'bg-gray-400');
                startTrackingBtn.classList.add('pulse-btn');
                startTrackingBtn.textContent = 'Mulai Pelacakan';
            }
            
            // Send location data to server
            function sendLocationToServer(locationData) {
                fetch(`/api/bus-location/${busId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(locationData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        addStatusMessage(`Lokasi berhasil dikirim (${formatTime(new Date())})`);
                    } else {
                        addStatusMessage('Gagal mengirim lokasi: ' + data.message, true);
                    }
                })
                .catch(error => {
                    addStatusMessage('Error: ' + error.message, true);
                });
            }
            
            // Add status message
            function addStatusMessage(message, isError = false) {
                const messageElement = document.createElement('div');
                messageElement.className = `p-2 rounded-md text-sm ${isError ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`;
                messageElement.textContent = message;
                
                // Add to status container
                statusMessages.prepend(messageElement);
                
                // Keep only the last 5 messages
                const messages = statusMessages.querySelectorAll('div');
                if (messages.length > 5) {
                    statusMessages.removeChild(messages[messages.length - 1]);
                }
            }
            
            // Show notification modal
            function showNotification(title, message) {
                notificationContent.innerHTML = `
                    <h3 class="text-xl font-bold mb-2">${title}</h3>
                    <p>${message}</p>
                `;
                notificationModal.classList.remove('hidden');
            }
            
            // Format date and time
            function formatDateTime(date) {
                return date.toLocaleDateString('id-ID', { 
                    day: '2-digit', 
                    month: 'short', 
                    year: 'numeric'
                }) + ' ' + date.toLocaleTimeString('id-ID');
            }
            
            // Format time only
            function formatTime(date) {
                return date.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit'
                });
            }
        });
    </script>
</body>
</html>