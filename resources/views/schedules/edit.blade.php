@extends('layouts.admin')

@section('title', 'Edit Schedule')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Schedule</h1>
            <a href="{{ route('admin.schedules.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                Back to List
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bus Selection -->
                    <div>
                        <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">Bus *</label>
                        <select name="bus_id" id="bus_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bus_id') border-red-500 @enderror">
                            <option value="">Select Bus</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" {{ old('bus_id', $schedule->bus_id) == $bus->id ? 'selected' : '' }}>
                                    {{ $bus->bus_number }} - {{ $bus->bus_name }} ({{ $bus->bus_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('bus_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Route Selection -->
                    <div>
                        <label for="route_id" class="block text-sm font-medium text-gray-700 mb-2">Route *</label>
                        <select name="route_id" id="route_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('route_id') border-red-500 @enderror">
                            <option value="">Select Route</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id) == $route->id ? 'selected' : '' }}>
                                    {{ $route->origin }} to {{ $route->destination }} ({{ $route->distance }} km)
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departure Date -->
                    <div>
                        <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-2">Departure Date *</label>
                        <input type="date" name="departure_date" id="departure_date" 
                               value="{{ old('departure_date', $schedule->departure_date) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('departure_date') border-red-500 @enderror">
                        @error('departure_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departure Time -->
                    <div>
                        <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">Departure Time *</label>
                        <input type="time" name="departure_time" id="departure_time" 
                               value="{{ old('departure_time', $schedule->departure_time) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('departure_time') border-red-500 @enderror">
                        @error('departure_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arrival Time -->
                    <div>
                        <label for="arrival_time" class="block text-sm font-medium text-gray-700 mb-2">Arrival Time *</label>
                        <input type="time" name="arrival_time" id="arrival_time" 
                               value="{{ old('arrival_time', $schedule->arrival_time) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('arrival_time') border-red-500 @enderror">
                        @error('arrival_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fare -->
                    <div>
                        <label for="fare" class="block text-sm font-medium text-gray-700 mb-2">Fare ($) *</label>
                        <input type="number" name="fare" id="fare" step="0.01" min="0"
                               value="{{ old('fare', $schedule->fare) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fare') border-red-500 @enderror">
                        @error('fare')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Seats -->
                    <div>
                        <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">Total Seats *</label>
                        <input type="number" name="total_seats" id="total_seats" min="1"
                               value="{{ old('total_seats', $schedule->total_seats) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_seats') border-red-500 @enderror">
                        @error('total_seats')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Available Seats -->
                    <div>
                        <label for="available_seats" class="block text-sm font-medium text-gray-700 mb-2">Available Seats *</label>
                        <input type="number" name="available_seats" id="available_seats" min="0"
                               value="{{ old('available_seats', $schedule->available_seats) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_seats') border-red-500 @enderror">
                        @error('available_seats')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="scheduled" {{ old('status', $schedule->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="delayed" {{ old('status', $schedule->status) == 'delayed' ? 'selected' : '' }}>Delayed</option>
                            <option value="cancelled" {{ old('status', $schedule->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="completed" {{ old('status', $schedule->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boarding Points -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Boarding Points</label>
                    <div id="boarding-points-container">
                        @if(count($schedule->boarding_points) > 0)
                            @foreach($schedule->boarding_points as $point)
                            <div class="boarding-point-item flex gap-2 mb-2">
                                <input type="text" name="boarding_points[]" value="{{ $point }}" placeholder="Enter boarding point"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
                            </div>
                            @endforeach
                        @else
                            <div class="boarding-point-item flex gap-2 mb-2">
                                <input type="text" name="boarding_points[]" placeholder="Enter boarding point"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addBoardingPoint()" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Add Boarding Point
                    </button>
                </div>

                <!-- Dropping Points -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dropping Points</label>
                    <div id="dropping-points-container">
                        @if(count($schedule->dropping_points) > 0)
                            @foreach($schedule->dropping_points as $point)
                            <div class="dropping-point-item flex gap-2 mb-2">
                                <input type="text" name="dropping_points[]" value="{{ $point }}" placeholder="Enter dropping point"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
                            </div>
                            @endforeach
                        @else
                            <div class="dropping-point-item flex gap-2 mb-2">
                                <input type="text" name="dropping_points[]" placeholder="Enter dropping point"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addDroppingPoint()" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Add Dropping Point
                    </button>
                </div>

                <!-- Amenities -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                        $amenities = ['WiFi', 'AC', 'Water Bottle', 'Charging Point', 'Reading Light', 'Blanket', 'Entertainment', 'Snacks', 'First Aid'];
                        $selectedAmenities = $schedule->amenities ?? [];
                        @endphp
                        @foreach($amenities as $amenity)
                        <label class="flex items-center">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                   {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}
                                   class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $amenity }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.schedules.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addBoardingPoint() {
    const container = document.getElementById('boarding-points-container');
    const div = document.createElement('div');
    div.className = 'boarding-point-item flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="boarding_points[]" placeholder="Enter boarding point"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
    `;
    container.appendChild(div);
}

function addDroppingPoint() {
    const container = document.getElementById('dropping-points-container');
    const div = document.createElement('div');
    div.className = 'dropping-point-item flex gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="dropping_points[]" placeholder="Enter dropping point"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="removePoint(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Remove</button>
    `;
    container.appendChild(div);
}

function removePoint(button) {
    button.parentElement.remove();
}

// Auto-update available seats when total seats changes
document.getElementById('total_seats').addEventListener('change', function() {
    const availableSeats = document.getElementById('available_seats');
    if (availableSeats.value > this.value) {
        availableSeats.value = this.value;
    }
    availableSeats.max = this.value;
});
</script>
@endpush
@endsection