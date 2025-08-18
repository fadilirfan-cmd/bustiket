
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Pesan Tiket - Medal Sekarwangi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .seat {
            transition: all 0.3s ease;
        }
        .seat:not(.booked):hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .seat.available {
            background-color: #10b981;
            color: white;
        }
        .seat.selected {
            background-color: #3b82f6;
            color: white;
        }
        .seat.booked {
            background-color: #ef4444;
            color: white;
            cursor: not-allowed;
            opacity: 0.8;
        }
        .pulse {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
        .aisle {
            background: repeating-linear-gradient(
                45deg,
                #f3f4f6,
                #f3f4f6 5px,
                #e5e7eb 5px,
                #e5e7eb 10px
            );
        }
        .bus-front {
            background: linear-gradient(to bottom, #374151, #1f2937);
            border-radius: 100% 100% 0 0 / 80% 80% 0 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- ===== HEADER (sama seperti home) ===== -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('logo.jpg') }}" class="h-10" alt="logo">
            <span class="text-xl font-bold text-red-600">Medal <span class="text-gray-800">Sekarwangi</span></span>
        </div>
        <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600">← Kembali</a>
    </div>
</header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-red-600 text-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-2">{{ $schedule->route->origin }} → {{ $schedule->route->destination }}</h2>
            <div class="flex flex-wrap gap-4 text-sm text-white-600">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Keberangkatan: {{ number_format($schedule->price,0) }}</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Waktu: {{ $schedule->departure_time }}</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    <span>Harga: Rp {{ number_format($schedule->price,0) }} per kursi</span>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Bus Tipe: {{ $schedule->bus->type }}</span>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Seat Selection Section -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Pilih Kursi</h2>
                    
                    <!-- Seat Legend -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <div class="flex items-center">
                            <div class="seat available w-8 h-8 rounded-md flex items-center justify-center mr-2">A</div>
                            <span class="text-sm">Tersedia</span>
                        </div>
                        <div class="flex items-center">
                            <div class="seat selected w-8 h-8 rounded-md flex items-center justify-center mr-2">S</div>
                            <span class="text-sm">Dipilih</span>
                        </div>
                        <div class="flex items-center">
                            <div class="seat booked w-8 h-8 rounded-md flex items-center justify-center mr-2">B</div>
                            <span class="text-sm">Terisi</span>
                        </div>
                    </div>
                    
                    <!-- Bus Layout -->
                    <div class="bus-layout">
                        <!-- Bus Front (Driver's Area) -->
                        <div class="bus-front h-16 mb-4 flex items-center justify-center text-white font-bold">
                            <span>Pengemudi dan Kru</span>
                        </div>
                        
                        <!-- Seats Container -->
                        <div class="seats-container" id="seatsContainer">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <!-- Bus Rear (Door) -->
                        <div class="mt-4 bg-gray-700 text-white text-center py-2 rounded-b-lg">
                            <span>Keluar</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Form Section -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Pilihan Kursi</h2>
                    <div id="selectedSeatsContainer" class="min-h-24 mb-4">
                        <p class="text-gray-500 text-center py-4">Kursi Tidak Dipilih</p>
                    </div>
                    
                    <div class="border-t pt-4">
    
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span id="totalPrice">Rp 0</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Informasi Penumpang</h2>
                    <form id="passengerForm">
                        <div class="mb-4">
                            <label for="fullName" class="block text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" id="fullName" name="fullName" required
                                   class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 mb-2">Nomor Whatsapp</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 mb-2">Lokasi Jemput</label>
                            <input type="text" id="lokasi" name="lokasi" required
                                   class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </form>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Metode Bayar</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="paymentMethod" value="cash" class="mr-3">
                            <span>Tunai Ke Agen</span>
                        </label>
                    </div>
                </div>
                
                <button id="bookButton" disabled
                        class="w-full bg-red-600 text-white py-3 rounded-md font-bold hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Ambil Tiket
                </button>
            </div>
        </div>
    </main>
    
    <!-- Booking Confirmation Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-8 max-w-md w-full">
            <h3 class="text-2xl font-bold mb-4 text-center">Booking Successful!</h3>
            <div class="mb-6">
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                    <p class="text-green-800 font-medium">Your booking has been confirmed.</p>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-medium" id="bookingId">BUS-123456</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Selected Seats:</span>
                        <span class="font-medium" id="modalSeats"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Passenger:</span>
                        <span class="font-medium" id="modalPassenger"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium" id="modalAmount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium" id="modalPayment"></span>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button id="closeModalButton" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Configuration
        const config = {
            rows: 10,
            leftSeats: 2,
            rightSeats: 3,
            pricePerSeat: 85000,
            maxSeatsSelection: 6
        };
        
        // Seat data with pre-booked seats
        const bookedSeats = [3, 8, 12, 17, 22, 27, 33, 38, 41, 47];
        let selectedSeats = [];
        
        // DOM Elements
        const seatsContainer = document.getElementById('seatsContainer');
        const selectedSeatsContainer = document.getElementById('selectedSeatsContainer');
        const totalPriceElement = document.getElementById('totalPrice');
        const bookButton = document.getElementById('bookButton');
        const bookingModal = document.getElementById('bookingModal');
        const closeModalButton = document.getElementById('closeModalButton');
        
        // Generate seat layout
        function generateSeatLayout() {
            for (let row = 1; row <= config.rows; row++) {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'flex justify-center mb-2';
                
                // Row number indicator
                const rowNumber = document.createElement('div');
                rowNumber.className = 'w-8 h-8 flex items-center justify-center text-gray-500 font-bold';
                rowNumber.textContent = row;
                
                // Container for all seats in this row
                const seatsRow = document.createElement('div');
                seatsRow.className = 'flex';
                
                // Left side seats
                const leftSeats = document.createElement('div');
                leftSeats.className = 'flex gap-2 mr-4';
                
                for (let i = 1; i <= config.leftSeats; i++) {
                    const seatNumber = (row - 1) * (config.leftSeats + config.rightSeats) + i;
                    const seat = createSeatElement(seatNumber);
                    leftSeats.appendChild(seat);
                }
                
                // Aisle
                const aisle = document.createElement('div');
                aisle.className = 'aisle w-8 h-8 rounded-md mx-2';
                
                // Right side seats
                const rightSeats = document.createElement('div');
                rightSeats.className = 'flex gap-2 ml-4';
                
                for (let i = 1; i <= config.rightSeats; i++) {
                    const seatNumber = (row - 1) * (config.leftSeats + config.rightSeats) + config.leftSeats + i;
                    const seat = createSeatElement(seatNumber);
                    rightSeats.appendChild(seat);
                }
                
                seatsRow.appendChild(leftSeats);
                seatsRow.appendChild(aisle);
                seatsRow.appendChild(rightSeats);
                
                rowDiv.appendChild(rowNumber);
                rowDiv.appendChild(seatsRow);
                seatsContainer.appendChild(rowDiv);
            }
        }
        
        // Create a seat element
        function createSeatElement(seatNumber) {
            const seat = document.createElement('div');
            const isBooked = bookedSeats.includes(seatNumber);
            
            seat.className = `seat w-8 h-8 rounded-md flex items-center justify-center font-medium cursor-pointer ${isBooked ? 'booked' : 'available'}`;
            seat.textContent = seatNumber;
            seat.dataset.seatNumber = seatNumber;
            
            if (!isBooked) {
                seat.addEventListener('click', () => toggleSeatSelection(seatNumber, seat));
            }
            
            return seat;
        }
        
        // Toggle seat selection
        function toggleSeatSelection(seatNumber, seatElement) {
            const seatIndex = selectedSeats.indexOf(seatNumber);
            
            if (seatIndex === -1) {
                // If seat is not selected, select it
                if (selectedSeats.length >= config.maxSeatsSelection) {
                    alert(`You can select a maximum of ${config.maxSeatsSelection} seats.`);
                    return;
                }
                
                selectedSeats.push(seatNumber);
                seatElement.classList.remove('available');
                seatElement.classList.add('selected');
                seatElement.classList.add('pulse');
            } else {
                // If seat is already selected, deselect it
                selectedSeats.splice(seatIndex, 1);
                seatElement.classList.remove('selected');
                seatElement.classList.remove('pulse');
                seatElement.classList.add('available');
            }
            
            updateSelectedSeatsDisplay();
        }
        
        // Update selected seats display
        function updateSelectedSeatsDisplay() {
            if (selectedSeats.length === 0) {
                selectedSeatsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada Kursi yang dipilih</p>';
                totalPriceElement.textContent = 'Rp 0';
                bookButton.disabled = true;
                return;
            }
            
            // Sort seats for better display
            const sortedSeats = [...selectedSeats].sort((a, b) => a - b);
            
            let html = '<div class="space-y-2">';
            sortedSeats.forEach(seat => {
                html += `
                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded-md">
                        <span class="font-medium">Seat ${seat}</span>
                        <button class="text-red-500 hover:text-red-700" onclick="removeSeat(${seat})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            
            selectedSeatsContainer.innerHTML = html;
            
            // Update total price
            const totalPrice = selectedSeats.length * config.pricePerSeat;
            totalPriceElement.textContent = formatCurrency(totalPrice);
            
            // Enable/disable book button
            bookButton.disabled = false;
        }
        
        // Remove a seat from selection
        function removeSeat(seatNumber) {
            const seatIndex = selectedSeats.indexOf(seatNumber);
            if (seatIndex !== -1) {
                selectedSeats.splice(seatIndex, 1);
                
                // Update the seat element
                const seatElement = document.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
                if (seatElement) {
                    seatElement.classList.remove('selected');
                    seatElement.classList.remove('pulse');
                    seatElement.classList.add('available');
                }
                
                updateSelectedSeatsDisplay();
            }
        }
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
        
        // Generate booking ID
        function generateBookingId() {
            const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase();
            return `BUS-${randomPart}`;
        }
        
        // Handle booking submission
        function handleBooking() {
            const fullName = document.getElementById('fullName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            
            if (!fullName || !phone) {
                alert('Please fill in all required fields.');
                return;
            }
            
            if (selectedSeats.length === 0) {
                alert('Please select at least one seat.');
                return;
            }
            
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const totalAmount = selectedSeats.length * config.pricePerSeat;
            const bookingId = generateBookingId();
            
            // Update modal content
            document.getElementById('bookingId').textContent = bookingId;
            document.getElementById('modalSeats').textContent = selectedSeats.sort((a, b) => a - b).join(', ');
            document.getElementById('modalPassenger').textContent = fullName;
            document.getElementById('modalAmount').textContent = formatCurrency(totalAmount);
            document.getElementById('modalPayment').textContent = formatPaymentMethod(paymentMethod);
            
            // Show modal
            bookingModal.classList.remove('hidden');
            
            // Mark selected seats as booked
            selectedSeats.forEach(seatNumber => {
                const seatElement = document.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
                if (seatElement) {
                    seatElement.classList.remove('selected');
                    seatElement.classList.remove('pulse');
                    seatElement.classList.add('booked');
                    seatElement.removeEventListener('click', toggleSeatSelection);
                }
                bookedSeats.push(seatNumber);
            });
            
            // Reset selection
            selectedSeats = [];
            updateSelectedSeatsDisplay();
            
            // Reset form
            document.getElementById('passengerForm').reset();
        }
        
        // Format payment method for display
        function formatPaymentMethod(method) {
            const methodMap = {
                'transfer_bca': 'Transfer BCA',
                'transfer_bni': 'Transfer BNI',
                'transfer_mandiri': 'Transfer Mandiri',
                'cash': 'Cash on Departure'
            };
            
            return methodMap[method] || method;
        }
        
        // Close modal
        function closeModal() {
            bookingModal.classList.add('hidden');
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            generateSeatLayout();
            bookButton.addEventListener('click', handleBooking);
            closeModalButton.addEventListener('click', closeModal);
        });
    </script>
</body>
</html>

