{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - WhatsApp OTP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-black via-gray-900 to-red-900">
        <div class="bg-gray-900 p-8 rounded-lg shadow-2xl w-full max-w-md border border-red-800">
            
        
            {{-- Logo/Brand --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-50 h-50 bg-black-600 rounded-full mb-4">
                <img src="{{ asset('logo.jpg') }}" 
                         alt="BusTrack Logo" 
                         class="w-50 h-50 mx-auto object-contain">
                </div>
                <h2 class="text-3xl font-bold text-white">PT <span class="text-red-600">Medal Sekarwangi BUS</span></h2>
                <p class="text-gray-400 mt-2">Login dengan WhatsApp</p>
            </div>

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4 text-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Terjadi kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside ml-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- WhatsApp Input --}}
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-white mb-2">
                        Nomor WhatsApp
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-red-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </span>
                        </div>
                        <input 
                            type="text" 
                            name="whatsapp" 
                            id="whatsapp" 
                            value="{{ old('whatsapp') }}"
                            class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 pr-3 py-3 @error('whatsapp') border-red-500 @enderror"
                            placeholder="6281234567890"
                            required
                            autofocus
                        >
                    </div>
                    @error('whatsapp')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">
                        Contoh: 6281234567890 (gunakan kode negara tanpa +)
                    </p>
                </div>



                {{-- Submit Button --}}
                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-red-500 transition duration-150 ease-in-out"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-red-500 group-hover:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        Kirim OTP
                    </button>
                </div>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-900 text-gray-400">
                            Belum punya akun?
                        </span>
                    </div>
                </div>

                {{-- Register Link --}}
                <div>
                    <a 
                        href="{{ route('register') }}" 
                        class="w-full flex justify-center py-3 px-4 border border-red-600 text-sm font-medium rounded-md text-red-600 bg-transparent hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-red-500 transition duration-150 ease-in-out"
                    >
                        Daftar Sekarang
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- JavaScript untuk Auto-format WhatsApp --}}
    <script>
        document.getElementById('whatsapp').addEventListener('input', function(e) {
            let value = e.target.value;
            // Hanya izinkan angka
            value = value.replace(/[^0-9]/g, '');
            // Pastikan diawali dengan 62
            if (value.length > 0 && !value.startsWith('62')) {
                value = '62' + value.replace(/^0+/, '');
            }
            e.target.value = value;
        });

        // Validasi sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const whatsappInput = document.getElementById('whatsapp');
            const whatsapp = whatsappInput.value;
            
            // Cek panjang nomor
            if (whatsapp.length < 10 || whatsapp.length > 15) {
                e.preventDefault();
                alert('Nomor WhatsApp harus antara 10-15 digit');
                return false;
            }
            
            // Cek format nomor
            if (!whatsapp.startsWith('62')) {
                e.preventDefault();
                alert('Nomor WhatsApp harus diawali dengan 62');
                return false;
            }
        });
    </script>
</body>
</html>