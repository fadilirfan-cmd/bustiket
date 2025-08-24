{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            {{-- Alert Messages --}}
            <div id="alertContainer"></div>

            {{-- Step 1: Phone & Role Selection --}}
            <div id="step1" class="space-y-6">
                {{-- Role Selection --}}
                <div>
                    <label class="block text-sm font-medium text-white mb-2">
                        Pilih Jenis Akun
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" 
                                onclick="selectRole('admin')"
                                id="roleAdmin"
                                class="role-btn bg-gray-800 border-2 border-gray-700 text-white p-4 rounded-lg hover:border-red-500 transition-all">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            <span class="font-semibold">Admin</span>
                        </button>
                        <button type="button" 
                                onclick="selectRole('pic_bus')"
                                id="rolePIC"
                                class="role-btn bg-gray-800 border-2 border-gray-700 text-white p-4 rounded-lg hover:border-red-500 transition-all">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                            </svg>
                            <span class="font-semibold">PIC Bus</span>
                        </button>
                    </div>
                </div>

                {{-- Bus Selection (for PIC) --}}
                <div id="busSelection" class="hidden">
                    <label for="bus_id" class="block text-sm font-medium text-white mb-2">
                        Pilih Bus
                    </label>
                    <select id="bus_id" 
                            class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full py-3">
                        <option value="">-- Pilih Bus --</option>
                        @foreach(\App\Models\Bus::select('bus_id','bus_number','bus_name')->get() as $bus)
                            <option value="{{ $bus->bus_id }}">{{ $bus->bus_number }} - {{ $bus->bus_name }}</option>
                        @endforeach
                    </select>
                </div>

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
                            id="whatsapp" 
                            class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 pr-3 py-3"
                            placeholder="6281234567890"
                            required
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-400">
                        Contoh: 6281234567890 (gunakan kode negara tanpa +)
                    </p>
                </div>

                {{-- Send OTP Button --}}
                <button 
                    onclick="sendOTP()"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-red-500 transition duration-150 ease-in-out"
                >
                    <span id="btnText">Kirim OTP</span>
                    <span id="btnLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>

            {{-- Step 2: OTP Verification --}}
            <div id="step2" class="hidden space-y-6">
                <div class="text-center">
                    <p class="text-white mb-2">Kode OTP telah dikirim ke</p>
                    <p class="text-red-500 font-semibold text-lg" id="phoneDisplay"></p>
                    <p class="text-gray-400 text-sm mt-2" id="selectedInfo"></p>
                </div>

                {{-- OTP Input Boxes --}}
                <div>
                    <label class="block text-sm font-medium text-white mb-2 text-center">
                        Masukkan Kode OTP
                    </label>
                    <div class="flex justify-center space-x-2">
                        @for($i = 1; $i <= 6; $i++)
                            <input 
                                type="text" 
                                id="otp{{ $i }}" 
                                maxlength="1" 
                                class="otp-input w-12 h-12 text-center text-xl font-bold bg-gray-800 border-2 border-gray-700 text-white rounded-lg focus:ring-red-500 focus:border-red-500"
                                onkeyup="moveToNext(this, 'otp{{ $i+1 }}')"
                                onkeydown="moveToPrevious(event, 'otp{{ $i-1 }}')"
                            >
                        @endfor
                    </div>
                </div>

                {{-- Timer --}}
                <div class="text-center">
                    <p class="text-gray-400 text-sm">
                        OTP berlaku selama: <span id="timer" class="text-red-500 font-semibold">05:00</span>
                    </p>
                </div>

                {{-- Verify Button --}}
                <button 
                    onclick="verifyOTP()"
                    class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-green-500 transition duration-150 ease-in-out"
                >
                    <span id="verifyBtnText">Verifikasi OTP</span>
                    <span id="verifyBtnLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>

                {{-- Resend OTP --}}
                <div class="text-center">
                    <button 
                        id="resendBtn"
                        onclick="resendOTP()"
                        class="text-red-500 hover:text-red-400 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                    >
                        Kirim Ulang OTP
                    </button>
                </div>

                {{-- Back Button --}}
                <div class="text-center">
                    <button 
                        onclick="backToStep1()"
                        class="text-gray-400 hover:text-white text-sm"
                    >
                        ← Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let selectedRole = '';
        let timerInterval;
        let timeLeft = 300; // 5 minutes
        
        // Configure axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        
        function selectRole(role) {
            selectedRole = role;
            
            // Update button styles
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('border-red-500', 'bg-red-900');
                btn.classList.add('border-gray-700', 'bg-gray-800');
            });
            
            const selectedBtn = role === 'admin' ? document.getElementById('roleAdmin') : document.getElementById('rolePIC');
            selectedBtn.classList.remove('border-gray-700', 'bg-gray-800');
            selectedBtn.classList.add('border-red-500', 'bg-red-900');
            
            // Show/hide bus selection
            if (role === 'pic_bus') {
                document.getElementById('busSelection').classList.remove('hidden');
            } else {
                document.getElementById('busSelection').classList.add('hidden');
            }
        }
        
        async function sendOTP() {
            const whatsapp = document.getElementById('whatsapp').value;
            const busId = document.getElementById('bus_id').value;
            
            // Validation
            if (!selectedRole) {
                showAlert('Silakan pilih jenis akun terlebih dahulu', 'error');
                return;
            }
            
            if (selectedRole === 'pic_bus' && !busId) {
                showAlert('Silakan pilih bus terlebih dahulu', 'error');
                return;
            }
            
            if (!whatsapp || whatsapp.length < 10) {
                showAlert('Masukkan nomor WhatsApp yang valid', 'error');
                return;
            }
            
            // Show loading
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoading').classList.remove('hidden');
            
            try {
                const response = await axios.post('/api/auth/send-otp', {
                    whatsapp: whatsapp,
                    role: selectedRole,
                    bus_id: busId
                });
                
                if (response.data.success) {
                    // Move to step 2
                    document.getElementById('step1').classList.add('hidden');
                    document.getElementById('step2').classList.remove('hidden');
                    
                    // Display info
                    document.getElementById('phoneDisplay').textContent = whatsapp;
                    
                    let info = selectedRole === 'admin' ? 'Admin' : 'PIC Bus';
                    if (selectedRole === 'pic_bus') {
                        const busSelect = document.getElementById('bus_id');
                        info += ` - ${busSelect.options[busSelect.selectedIndex].text}`;
                    }
                    document.getElementById('selectedInfo').textContent = info;
                    
                    // Start timer
                    startTimer();
                    
                    // Focus first OTP input
                    document.getElementById('otp1').focus();
                    
                    showAlert('OTP berhasil dikirim ke WhatsApp Anda', 'success');
                }
            } catch (error) {
                showAlert(error.response?.data?.message || 'Gagal mengirim OTP', 'error');
            } finally {
                document.getElementById('btnText').classList.remove('hidden');
                document.getElementById('btnLoading').classList.add('hidden');
            }
        }
        
        async function verifyOTP() {
            let otp = '';
            for (let i = 1; i <= 6; i++) {
                otp += document.getElementById('otp' + i).value;
            }
            
            if (otp.length !== 6) {
                showAlert('Masukkan 6 digit kode OTP', 'error');
                return;
            }
            
            // Show loading
            document.getElementById('verifyBtnText').classList.add('hidden');
            document.getElementById('verifyBtnLoading').classList.remove('hidden');
            
            try {
                const response = await axios.post('/api/auth/verify-otp', {
                    whatsapp: document.getElementById('whatsapp').value,
                    otp: otp,
                    role: selectedRole,
                    bus_id: document.getElementById('bus_id').value
                });
                
                if (response.data.success) {
                    showAlert('Login berhasil! Mengalihkan...', 'success');
                    
                    // Save token and redirect
                    localStorage.setItem('auth_token', response.data.token);
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                    
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1500);
                }
            } catch (error) {
                showAlert(error.response?.data?.message || 'OTP tidak valid', 'error');
                
                // Clear OTP inputs
                for (let i = 1; i <= 6; i++) {
                    document.getElementById('otp' + i).value = '';
                }
                document.getElementById('otp1').focus();
            } finally {
                document.getElementById('verifyBtnText').classList.remove('hidden');
                document.getElementById('verifyBtnLoading').classList.add('hidden');
            }
        }
        
        async function resendOTP() {
            document.getElementById('resendBtn').disabled = true;
            await sendOTP();
        }
        
        function backToStep1() {
            clearInterval(timerInterval);
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            
            // Clear OTP inputs
            for (let i = 1; i <= 6; i++) {
                document.getElementById('otp' + i).value = '';
            }
        }
        
        function startTimer() {
            timeLeft = 300;
            timerInterval = setInterval(() => {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('timer').textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timer').textContent = 'Kadaluarsa';
                    document.getElementById('resendBtn').disabled = false;
                }
            }, 1000);
        }
        
        function moveToNext(current, nextFieldID) {
            if (current.value.length >= current.maxLength) {
                const nextField = document.getElementById(nextFieldID);
                if (nextField) {
                    nextField.focus();
                }
            }
        }
        
        function moveToPrevious(event, previousFieldID) {
            if (event.key === 'Backspace' && event.target.value === '') {
                const previousField = document.getElementById(previousFieldID);
                if (previousField) {
                    previousField.focus();
                }
            }
        }
        
        function showAlert(message, type) {
            const alertClass = type === 'success' 
                ? 'bg-green-900 border-green-700 text-green-100' 
                : 'bg-red-900 border-red-700 text-red-100';
                
            const alertHtml = `
                <div class="${alertClass} border px-4 py-3 rounded mb-4 text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            ${type === 'success' 
                                ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                                : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                            }
                        </svg>
                        ${message}
                    </div>
                </div>
            `;
            
            document.getElementById('alertContainer').innerHTML = alertHtml;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                document.getElementById('alertContainer').innerHTML = '';
            }, 5000);
        }
        
        // Auto-format WhatsApp number
        document.getElementById('whatsapp').addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/[^0-9]/g, '');
            if (value.length > 0 && !value.startsWith('62')) {
                value = '62' + value.replace(/^0+/, '');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>