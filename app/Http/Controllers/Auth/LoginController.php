<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FonteOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Menampilkan form login
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login dengan nomor WhatsApp
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'whatsapp' => 'required|string|min:10|max:15',
        ], [
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'whatsapp.min' => 'Nomor WhatsApp minimal 10 digit',
            'whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit',
        ]);

        // Cari user berdasarkan nomor WhatsApp
        $user = User::where('whatsapp', $request->whatsapp)->first();

        // Jika user tidak ditemukan
        if (!$user) {
            return back()
                ->withInput($request->only('whatsapp'))
                ->withErrors(['whatsapp' => 'Nomor WhatsApp tidak terdaftar']);
        }

        // Cek apakah user aktif
        if ($user->status === 'inactive') {
            return back()
                ->withInput($request->only('whatsapp'))
                ->withErrors(['whatsapp' => 'Akun Anda tidak aktif. Silakan hubungi admin.']);
        }

        try {
            // Generate dan kirim OTP
            $otpService = new FonteOtpService();
            $otp = $otpService->generateAndSendOtp($user->whatsapp);

            if (!$otp) {
                return back()
                    ->withInput($request->only('whatsapp'))
                    ->withErrors(['whatsapp' => 'Gagal mengirim OTP. Silakan coba lagi']);
            }

            // Simpan OTP ke database
            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
                'otp_attempts' => 0,
            ]);

            // Simpan user ID di session
            Session::put('otp_user_id', $user->id);
            Session::put('otp_whatsapp', $user->whatsapp);

            // Redirect ke halaman verifikasi OTP
            return redirect()->route('otp.verify.form')
                ->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda');

        } catch (\Exception $e) {
            // Log error
            \Log::error('OTP Send Error: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('whatsapp'))
                ->withErrors(['whatsapp' => 'Terjadi kesalahan. Silakan coba lagi']);
        }
    }

    /**
     * Menampilkan form verifikasi OTP
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOtpForm()
    {
        // Cek apakah ada session otp_user_id
        if (!Session::has('otp_user_id')) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Sesi telah berakhir. Silakan login kembali']);
        }

        $whatsapp = Session::get('otp_whatsapp');
        
        // Sembunyikan nomor WhatsApp untuk keamanan
        $maskedWhatsapp = $this->maskWhatsapp($whatsapp);

        return view('auth.otp-verify', compact('maskedWhatsapp'));
    }

    /**
     * Memverifikasi OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        // Validasi input OTP
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.size' => 'Kode OTP harus 6 digit',
        ]);

        // Ambil user ID dari session
        $userId = Session::get('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Sesi telah berakhir. Silakan login kembali']);
        }

        // Cari user
        $user = User::find($userId);

        if (!$user) {
            Session::forget(['otp_user_id', 'otp_whatsapp']);
            return redirect()->route('login')
                ->withErrors(['error' => 'User tidak ditemukan']);
        }

        // Cek apakah OTP sudah kadaluarsa
        if (now()->greaterThan($user->otp_expires_at)) {
            $user->update(['otp' => null, 'otp_expires_at' => null]);
            Session::forget(['otp_user_id', 'otp_whatsapp']);
            
            return redirect()->route('login')
                ->withErrors(['error' => 'Kode OTP telah kadaluarsa. Silakan login kembali']);
        }

        // Cek jumlah percobaan OTP
        if ($user->otp_attempts >= 3) {
            Session::forget(['otp_user_id', 'otp_whatsapp']);
            
            return redirect()->route('login')
                ->withErrors(['error' => 'Terlalu banyak percobaan. Silakan login kembali']);
        }

        // Verifikasi OTP
        if (!Hash::check($request->otp, $user->otp)) {
            // Increment percobaan
            $user->increment('otp_attempts');
            
            $remainingAttempts = 3 - $user->otp_attempts;
            
            return back()
                ->withInput($request->only('otp'))
                ->withErrors(['otp' => "Kode OTP salah. Sisa percobaan: $remainingAttempts"]);
        }

        // Login user
        Auth::login($user);

        // Bersihkan OTP dan session
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Session::forget(['otp_user_id', 'otp_whatsapp']);

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if ($user->hasRole('agen')) {
            return redirect()->intended('/agen/dashboard');
        } elseif ($user->hasRole('penumpang')) {
            return redirect()->intended('/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Mengirim ulang OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOtp(Request $request)
    {
        // Cek apakah ada session otp_user_id
        if (!Session::has('otp_user_id')) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Sesi telah berakhir. Silakan login kembali']);
        }

        $userId = Session::get('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            Session::forget(['otp_user_id', 'otp_whatsapp']);
            return redirect()->route('login')
                ->withErrors(['error' => 'User tidak ditemukan']);
        }

        // Cek cooldown (30 detik)
        if ($user->otp_sent_at && now()->diffInSeconds($user->otp_sent_at) < 30) {
            return back()->withErrors(['error' => 'Tunggu 30 detik sebelum mengirim ulang']);
        }

        try {
            // Generate dan kirim OTP baru
            $otpService = new FonteOtpService();
            $otp = $otpService->generateAndSendOtp($user->whatsapp);

            if (!$otp) {
                return back()->withErrors(['error' => 'Gagal mengirim OTP. Silakan coba lagi']);
            }

            // Update OTP di database
            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
                'otp_attempts' => 0,
                'otp_sent_at' => now(),
            ]);

            return back()->with('success', 'Kode OTP baru telah dikirim');

        } catch (\Exception $e) {
            \Log::error('OTP Resend Error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Terjadi kesalahan. Silakan coba lagi']);
        }
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Menyembunyikan nomor WhatsApp untuk keamanan
     *
     * @param  string  $whatsapp
     * @return string
     */
    private function maskWhatsapp($whatsapp)
    {
        // Format: 6281234567890 -> 6281****7890
        if (strlen($whatsapp) > 8) {
            return substr($whatsapp, 0, 4) . '****' . substr($whatsapp, -4);
        }
        
        return $whatsapp;
    }
}