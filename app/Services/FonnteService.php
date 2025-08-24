<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private $apiUrl;
    private $token;
    public function __construct()
    {
        $this->apiUrl = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $this->token = config('services.fonnte.token');
    }
    public function sendOTP($phoneNumber, $otp)
    {
        try {
            // Format nomor telepon Indonesia
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            $message = "*SISTEM BUS TRACKING*\n\n";
            $message .= "Kode OTP Anda: *{$otp}*\n";
            $message .= "Berlaku selama 5 menit.\n\n";
            $message .= "_Jangan bagikan kode ini kepada siapapun._";
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->apiUrl, [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62'
            ]);
            if ($response->successful()) {
                Log::info('OTP sent successfully to ' . $phoneNumber);
                return true;
            }
            Log::error('Failed to send OTP: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte Service Error: ' . $e->getMessage());
            return false;
        }
    }
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 08 to 628
        if (substr($phone, 0, 2) == '08') {
            $phone = '628' . substr($phone, 2);
        }
        
        // Add 62 if not present
        if (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}