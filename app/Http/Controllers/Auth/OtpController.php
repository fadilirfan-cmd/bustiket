<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $otp = rand(100000, 999999); // Generate OTP

        // Kirim OTP via SMS menggunakan Twilio atau layanan SMS lain
        $sid = 'your_twilio_sid';
        $token = 'your_twilio_auth_token';
        $client = new Client($sid, $token);
        $client->messages->create(
            '+62' . $request->phone, // Nomor tujuan
            [
                'from' => '+1234567890', // Nomor Twilio
                'body' => "Kode OTP Anda adalah: $otp"
            ]
        );

        // Simpan OTP ke sesi atau database untuk verifikasi
        session(['otp' => $otp, 'phone' => $request->phone]);

        return redirect()->route('otp.verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        if ($request->otp == session('otp')) {
            // OTP valid, login pengguna atau arahkan ke halaman utama
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['otp' => 'OTP tidak valid']);
    }
}
