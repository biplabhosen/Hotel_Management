@foreach ($roomsWithStatus as $room)
    <div class="col-md-3">
        <div class="room-card h-100">

            <!-- Header -->
            <div class="d-flex justify-content-between mb-2">
                <div>
                    <strong class="d-flex align-items-center gap-2">
                        <i class="bi bi-door-open"></i>
                        Room {{ $room->room_number }}
                    </strong>
                    <small class="text-muted d-flex align-items-center gap-2">
                        <i class="bi bi-layers"></i>
                        Floor {{ $room->floor }}
                    </small>

                </div>

                <!-- Status -->
                <span class="badge-status status-{{ $room->range_status ?? $room->status }}">
                    @php $s = $room->range_status ?? $room->status; @endphp
                    @if ($s === 'available')
                        <i class="bi bi-check-circle me-1"></i>
                    @elseif ($s === 'occupied')
                        <i class="bi bi-person-fill-lock me-1"></i>
                    @elseif ($s === 'booked')
                        <i class="bi bi-calendar-check me-1"></i>
                    @endif
                    {{ ucfirst($s) }}
                </span>
            </div>

            <!-- Room Type -->
            <p class="room-meta mt-4 text-muted">
                <i class="bi bi-star-fill text-warning me-1"></i>
                {{ $room->roomType?->name }}
            </p>

            <p class="room-meta">
                <i class="bi bi-currency-dollar"></i>
                {{ $room->roomType?->price_per_night }} / night
            </p>

            <p class="room-meta">
                <i class="bi bi-moon-stars me-1"></i>
                {{ ucfirst($room->roomType?->bed_type) }} Bed
            </p>

            <p class="room-meta">
                <i class="bi bi-people me-1"></i>
                {{ $room->roomType?->capacity }} Adults
            </p>

            <!-- Amenities -->
            <div class="amenities mt-2 d-flex gap-2">
                @foreach ($room->roomType->amenities as $amenity)
                    <i class="bi bi-{{ $amenity->icon }}" data-bs-toggle="tooltip" title="{{ $amenity->name }}"></i>
                @endforeach
            </div>

            <!-- Status-wise Button -->
            <div class="mt-3">
                @php $s = $room->range_status ?? $room->status; @endphp
                @if ($s === 'available')
                    <button class="btn btn-success btn-sm w-100" data-bs-toggle="offcanvas" data-bs-target="#guestOffcanvas">
                        <i class="bi bi-person-plus me-1"></i> Add Guest
                    </button>
                @elseif ($s === 'occupied')
                    <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#guestModal">
                        <i class="bi bi-person-vcard me-1"></i> View Guest
                    </button>
                @elseif ($s === 'booked')
                    <button class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-calendar-plus me-1"></i> Book Now
                    </button>
                @endif
            </div>

        </div>
    </div>
@endforeach
