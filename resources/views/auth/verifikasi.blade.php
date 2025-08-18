<!-- resources/views/auth/otp-verify.blade.php -->
<x-guest-layout>
    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <div>
            <x-input-label for="otp" :value="__('Masukkan Kode OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verifikasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>