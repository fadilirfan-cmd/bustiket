@extends('layouts.admin')

@section('title', 'Tambah Bus')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Tambah Bus Baru</h1>
        <p class="text-gray-400 mt-2">Tambahkan armada bus baru ke sistem</p>
    </div>

    {{-- Form --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <form action="{{ route('admin.buses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Bus Code --}}
                <div>
                    <label for="bus_code" class="block text-sm font-medium text-gray-300 mb-2">
                        Kode Bus <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bus_code" id="bus_code" 
                           value="{{ old('bus_code') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('bus_code') border-red-500 @enderror"
                           placeholder="BUS001" required>
                    @error('bus_code')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bus Name --}}
                <div>
                    <label for="bus_name" class="block text-sm font-medium text-gray-300 mb-2">
                        Nama Bus <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bus_name" id="bus_name" 
                           value="{{ old('bus_name') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('bus_name') border-red-500 @enderror"
                           placeholder="Primajasa Express" required>
                    @error('bus_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Plate Number --}}
                <div>
                    <label for="plate_number" class="block text-sm font-medium text-gray-300 mb-2">
                        Plat Nomor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="plate_number" id="plate_number" 
                           value="{{ old('plate_number') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('plate_number') border-red-500 @enderror"
                           placeholder="B 1234 ABC" required>
                    @error('plate_number')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Capacity --}}
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-300 mb-2">
                        Kapasitas <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacity" id="capacity" 
                           value="{{ old('capacity', 40) }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('capacity') border-red-500 @enderror"
                           min="1" required>
                    @error('capacity')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-300 mb-2">
                        Tipe Bus <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" 
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('type') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="executive" {{ old('type') == 'executive' ? 'selected' : '' }}>Executive</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PIC --}}
                <div>
                    <label for="pic_id" class="block text-sm font-medium text-gray-300 mb-2">
                        PIC Bus
                    </label>
                    <select name="pic_id" id="pic_id" 
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none">
                        <option value="">-- Belum ada PIC --</option>
                        @foreach($pics as $pic)
                            <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                {{ $pic->name }} ({{ $pic->whatsapp }})
                            </option>
                        @endforeach
                    </select>
                    @error('pic_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image --}}
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-300 mb-2">
                        Foto Bus
                    </label>
                    <input type="file" name="image" id="image" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-red-500 focus:outline-none @error('image') border-red-500 @enderror"
                           accept="image/*">
                    @error('image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, max 2MB</p>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-4 mt-6 pt-6 border-t border-gray-700">
                <a href="{{ route('admin.buses.index') }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Simpan Bus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection