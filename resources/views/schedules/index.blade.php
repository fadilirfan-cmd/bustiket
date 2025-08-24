@extends('layouts.admin')

@section('title', 'Manajemen Jadwal')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Jadwal</h1>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bus</label>
                    <select name="bus_id" class="form-select">
                        <option value="">Semua Bus</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->name }} - {{ $bus->plate_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bus</th>
                            <th>Rute</th>
                            <th>Tanggal</th>
                            <th>Keberangkatan</th>
                            <th>Tiba</th>
                            <th>Harga</th>
                            <th>Kursi Tersedia</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->id }}</td>
                                <td>
                                    <a href="{{ route('admin.schedules.by-bus', $schedule->bus_id) }}">
                                        {{ $schedule->bus->name }}<br>
                                        <small class="text-muted">{{ $schedule->bus->plate_number }}</small>
                                    </a>
                                </td>
                                <td>
                                    {{ $schedule->route->origin }}<br>
                                    <i class="fas fa-arrow-down text-muted small"></i><br>
                                    {{ $schedule->route->destination }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d M Y') }}</td>
                                <td>{{ $schedule->departure_time }}</td>
                                <td>{{ $schedule->arrival_time }}</td>
                                <td>Rp {{ number_format($schedule->price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $schedule->available_seats > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $schedule->available_seats }} / {{ $schedule->bus->capacity }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge status-badge bg-{{ 
                                        $schedule->status == 'scheduled' ? 'info' : 
                                        ($schedule->status == 'ongoing' ? 'warning' : 
                                        ($schedule->status == 'completed' ? 'success' : 'danger')) 
                                    }}" data-schedule-id="{{ $schedule->id }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.schedules.show', $schedule) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary toggle-status" 
                                                data-id="{{ $schedule->id }}" title="Toggle Status">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada jadwal ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $schedules->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-status').click(function() {
        const scheduleId = $(this).data('id');
        const button = $(this);
        
        $.ajax({
            url: `/admin/schedules/${scheduleId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update badge
                    const badge = $(`.status-badge[data-schedule-id="${scheduleId}"]`);
                    badge.removeClass('bg-info bg-warning bg-success bg-danger');
                    
                    const statusColors = {
                        'scheduled': 'bg-info',
                        'ongoing': 'bg-warning',
                        'completed': 'bg-success',
                        'cancelled': 'bg-danger'
                    };
                    
                    badge.addClass(statusColors[response.status]);
                    badge.text(response.status.charAt(0).toUpperCase() + response.status.slice(1));
                    
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('Gagal mengubah status jadwal');
            }
        });
    });
});
</script>
@endpush
@endsection