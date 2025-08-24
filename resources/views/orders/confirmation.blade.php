
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
     <div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-green-500 p-4 text-white">
            <h1 class="text-xl font-bold text-center">Pemesanan Berhasil!</h1>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between mb-4 pb-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold">Order #{{ $order->order_number }}</h2>
                    <p class="text-gray-500 text-sm">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <button class="px-3 py-1 {{ $order->status == 'pending' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full text-sm font-medium">
                    {{ $order->status == 'pending' ? 'Sukses' : ucfirst($order->status) }}
                </button>
            </div>
            
            <div class="mb-6">
                <h3 class="text-gray-700 font-semibold mb-2">Detail Penumpang</h3>
                <p class="mb-1"><span class="text-gray-600">Nama:</span> {{ $order->passenger_name }}</p>
                <p class="mb-1"><span class="text-gray-600">Telepon:</span> {{ $order->passenger_phone }}</p>
                <p><span class="text-gray-600">Lokasi Jemput:</span> {{ $order->jemput }}</p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-gray-700 font-semibold mb-2">Detail Perjalanan</h3>
                <p class="mb-1"><span class="text-gray-600">Rute:</span> {{ $order->schedule->route->description ?? 'N/A' }}</p>
                <p class="mb-1"><span class="text-gray-600">Tanggal:</span> {{ \Carbon\Carbon::parse($order->schedule->departure_date ?? now())->format('d M Y') }}</p>
                <p class="mb-1"><span class="text-gray-600">Jam:</span> {{ \Carbon\Carbon::parse($order->schedule->departure_time ?? now())->format('H:i') }}</p>
                <p><span class="text-gray-600">Kursi:</span> {{ $order->seat_numbers }}</p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-gray-700 font-semibold mb-2">Detail Pembayaran</h3>
                <p class="mb-1"><span class="text-gray-600">Metode:</span> {{ str_replace('_', ' ', strtoupper($order->payment_method)) }}</p>
                <p class="text-xl font-bold text-red-600">Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>Catatan:</strong> Silahkan lakukan pembayaran ke kru bus saat sudah naik bus.
                </p>
            </div>
            
            <div class="flex justify-between">
                <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Kembali ke Beranda
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Cetak Tiket
                </button>
            </div>
        </div>
    </div>
</div>
    
  
</body>
</html>




