<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Bus;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    private $fonteToken = 'FrGrGaLDC9NN83ZvfuAm'; // Ganti dengan token Fonnte Anda
    public function sendOTP(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|regex:/^62[0-9]{9,13}$/',
            'role' => 'required|in:admin,pic_bus',
            //'bus_id' => 'required_if:role,pic_bus|exists:buses,bus_id'
        ]);
        
        $whatsapp = $request->whatsapp;
        $role = $request->role;
        $busId = $request->bus_id;
        
        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Check if user exists
        $user = User::where('whatsapp', $whatsapp)->first();
        
        if ($user) {
            // Validate role
            if ($user->role !== $role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp terdaftar dengan jenis akun yang berbeda'
                ], 400);
            }
            
            // Validate bus for PIC
            if ($role === 'pic_bus' && $user->bus_id != $busId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar sebagai PIC untuk bus ini'
                ], 400);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => 'User ' . substr($whatsapp, -4),
                'whatsapp' => $whatsapp,
                'password' => bcrypt($otp), // Temporary password
                'role' => $role,
                'bus_id' => $role === 'pic_bus' ? $busId : null
            ]);
        }
        
        // Update OTP
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5)
        ]);
        
        // Prepare message
        $busInfo = '';
        if ($role === 'pic_bus' && $busId) {
            $bus = Bus::find($busId);
            $busInfo = "\nBus: {$bus->bus_name} ({$bus->bus_code})";
        }
        
        $message = "🚌 *PT Medal Sekarwangi BUS*\n\n";
        $message .= "Kode OTP Anda: *{$otp}*\n";
        $message .= "Login sebagai: *" . ($role === 'admin' ? 'Admin' : 'PIC Bus') . "*{$busInfo}\n\n";
        $message .= "⏱️ Kode ini berlaku selama 5 menit.\n";
        $message .= "⚠️ *Jangan bagikan kode ini kepada siapapun.*";
        
        // Send OTP via Fonnte
        $response = Http::withHeaders([
            'Authorization' => $this->fonteToken
        ])->post('https://api.fonnte.com/send', [
            'target' => $whatsapp,
            'message' => $message,
            'countryCode' => '62'
        ]);
        
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil dikirim ke WhatsApp Anda'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
        ], 500);
    }
    
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required',
            'otp' => 'required|digits:6',
            'role' => 'required|in:admin,pic_bus',
            'bus_id' => 'nullable'
        ]);
        
        $user = User::where('whatsapp', $request->whatsapp)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa'
            ], 401);
        }
        
        // Verify role and bus
        if ($user->role !== $request->role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak sesuai'
            ], 401);
        }
        
        if ($request->role === 'pic_bus' && $user->bus_id != $request->bus_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bus tidak sesuai'
            ], 401);
        }
        
        // Clear OTP and mark as verified
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'is_verified' => true
        ]);
        
        // Login user
        Auth::login($user);
        
        // Create token for API
        //$token = $user->createToken('auth_token')->plainTextToken;
        
        // Determine redirect URL
        $redirect = $user->role === 'admin' 
            ? '/admin/dashboard' 
            : '/pic/dashboard';
        
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'whatsapp' => $user->whatsapp,
                'role' => $user->role,
                'bus_id' => $user->bus_id,
                'bus' => $user->bus ? [
                    'id' => $user->bus->id,
                    'name' => $user->bus->bus_name,
                    'code' => $user->bus->bus_code
                ] : null
            ],
            //'token' => $token,
            'redirect' => $redirect
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda telah berhasil logout');
    }
}