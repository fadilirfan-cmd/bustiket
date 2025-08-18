<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <title>Pesan Tiket - BusTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
<!-- ===== HEADER (sama seperti home) ===== -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('brand/logo.png') }}" class="h-10" alt="logo">
            <span class="text-xl font-bold text-red-600">BUS<span class="text-gray-800">TRACK</span></span>
        </div>
        <a href="{{ route('home') }}" class="text-gray-700 hover:text-red-600">← Kembali</a>
    </div>
</header>

<!-- ===== HERO ===== -->
<section class="bg-gradient-to-r from-red-600 to-red-700 text-white py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold">Pemesanan Tiket</h1>
        <p>{{ $schedule->route->origin }} → {{ $schedule->route->destination }}</p>
    </div>
</section>

<!-- ===== FORM ORDER ===== -->
<form id="orderForm" method="POST" action="{{ route('orders.store', $schedule) }}" class="container mx-auto px-4 py-8">
    @csrf
    <div class="grid md:grid-cols-3 gap-8">

        <!-- === LAYOUT KURSI === -->
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Pilih Kursi</h2>

            <!-- Legend -->
            <div class="flex items-center space-x-4 mb-4 text-sm">
                <div class="flex items-center"><div class="w-4 h-4 bg-green-500 rounded mr-2"></div>Tersedia</div>
                <div class="flex items-center"><div class="w-4 h-4 bg-red-500 rounded mr-2"></div>Terisi</div>
                <div class="flex items-center"><div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>Dipilih</div>
            </div>

            <!-- Layout Kursi (2-1-2) -->
            <div class="grid grid-cols-5 gap-2 text-center">
                <!-- Label kolom -->
                <div></div><div class="font-bold">1</div><div class="font-bold">2</div><div></div><div class="font-bold">3</div><div class="font-bold">4</div>

                @php
                    // kursi yang sudah dibooking
                    $booked = $schedule->bookings->pluck('seat_number')->toArray();
                @endphp

                @for($row = 1; $row <= 10; $row++)
                    <div class="font-bold">{{ $row }}</div>
                    @for($col = 1; $col <= 4; $col++)
                        @if($col == 3)
                            <div></div> {{-- lorong --}}
                        @endif
                        @php
                            $seat = $row.$col;
                            $isBooked = in_array($seat, $booked);
                        @endphp
                        <button type="button" 
                                data-seat="{{ $seat }}"
                                class="seat w-10 h-10 rounded text-xs font-semibold transition-colors
                                {{ $isBooked ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-green-500 text-white hover:bg-green-600' }}"
                                {{ $isBooked ? 'disabled' : '' }}>
                            {{ $seat }}
                        </button>
                    @endfor
                @endfor
            </div>

            <!-- Input tersembunyi untuk kursi yang dipilih -->
            <input type="hidden" name="seat_numbers" id="seatNumbers">
        </div>

        <!-- === FORM DATA PENUMPANG & PEMBAYARAN === -->
        <div class="bg-white rounded-lg shadow p-6 space-y-6">

            <!-- Data Penumpang -->
            <div>
                <h2 class="text-lg font-semibold mb-3 text-gray-800">Data Penumpang</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="passenger_name" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                        <input type="text" name="passenger_phone" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <h2 class="text-lg font-semibold mb-3 text-gray-800">Metode Pembayaran</h2>
                <div class="space-y-3">
                    @foreach(['transfer_bca', 'transfer_bni', 'transfer_mandiri', 'tunai_agen'] as $method)
                        <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="{{ $method }}" class="mr-3" required>
                            <span>
                                {{ str_replace('_', ' ', strtoupper($method)) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Ringkasan Harga -->
            <div class="border-t pt-4">
                <div class="flex justify-between text-lg font-semibold">
                    <span>Total:</span>
                    <span id="grandTotal">Rp 0</span>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" disabled id="submitBtn"
                    class="w-full bg-red-600 text-white py-3 rounded font-semibold hover:bg-red-700 disabled:opacity-50">
                Pesan Sekarang
            </button>
        </div>
    </div>
</form>

<script>
/* ===== LOGIKA KURSI ===== */
const seats = document.querySelectorAll('.seat:not(:disabled)');
const seatNumbersInput = document.getElementById('seatNumbers');
const grandTotalEl = document.getElementById('grandTotal');
const submitBtn = document.getElementById('submitBtn');
const pricePerSeat = {{ $schedule->price }};
let selectedSeats = [];

seats.forEach(btn => {
    btn.addEventListener('click', () => {
        const seat = btn.dataset.seat;
        if (selectedSeats.includes(seat)) {
            selectedSeats = selectedSeats.filter(s => s !== seat);
            btn.classList.remove('bg-blue-500');
            btn.classList.add('bg-green-500');
        } else {
            selectedSeats.push(seat);
            btn.classList.remove('bg-green-500');
            btn.classList.add('bg-blue-500');
        }
        updateSummary();
    });
});

function updateSummary() {
    const total = selectedSeats.length * pricePerSeat;
    grandTotalEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    seatNumbersInput.value = selectedSeats.join(',');
    submitBtn.disabled = selectedSeats.length === 0;
}
</script>
</body>
</html>