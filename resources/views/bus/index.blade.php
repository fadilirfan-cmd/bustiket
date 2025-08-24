@extends('layouts.admin')

@section('title', 'Manajemen Bus')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Manajemen Bus</h1>
            <p class="text-gray-400 mt-2">Kelola armada bus perusahaan</p>
        </div>
        <a href="{{ route('admin.buses.create') }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
            </svg>
            Tambah Bus
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <p class="text-gray-400 text-sm">Total Bus</p>
            <p class="text-2xl font-bold text-white">{{ $buses->total() }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <p class="text-gray-400 text-sm">Bus Aktif</p>
            <p class="text-2xl font-bold text-green-500">{{ $buses->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <p class="text-gray-400 text-sm">Bus Maintenance</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $buses->where('status', 'maintenance')->count() }}</p>
        </div>
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <p class="text-gray-400 text-sm">Bus Tidak Aktif</p>
            <p class="text-2xl font-bold text-red-500">{{ $buses->where('status', 'inactive')->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700 bg-gray-900">
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Bus</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Plat Nomor</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Kapasitas</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">PIC</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($buses as $bus)
                    <tr class="hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($bus->image)
                                <img src="{{ Storage::url($bus->image) }}" alt="{{ $bus->bus_name }}" 
                                     class="w-10 h-10 rounded-lg object-cover">
                                @else
                                <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <p class="text-white font-semibold">{{ $bus->bus_code }}</p>
                                    <p class="text-gray-400 text-sm">{{ $bus->bus_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $bus->plate_number }}</td>
                        <td class="px-6 py-4">
                            @if($bus->type == 'vip')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-600/20 text-yellow-500">VIP</span>
                            @elseif($bus->type == 'executive')
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-600/20 text-purple-500">Executive</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-600/20 text-gray-400">Regular</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $bus->capacity }} kursi</td>
                        <td class="px-6 py-4 text-gray-300">
                            @if($bus->pic)
                                {{ $bus->pic->name }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="toggleStatus({{ $bus->id }})" 
                                    class="status-toggle-{{ $bus->id }}">
                                @if($bus->status == 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-600/20 text-green-500">Aktif</span>
                                @elseif($bus->status == 'maintenance')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-600/20 text-yellow-500">Maintenance</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-600/20 text-red-500">Tidak Aktif</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.buses.tracking', $bus) }}" 
                                   class="text-blue-500 hover:text-blue-400" title="Tracking">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.buses.show', $bus) }}" 
                                   class="text-gray-400 hover:text-white" title="Detail">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.buses.edit', $bus) }}" 
                                   class="text-yellow-500 hover:text-yellow-400" title="Edit">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" 
                                      class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bus ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada data bus
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($buses->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $buses->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(busId) {
    fetch(`/admin/buses/${busId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection