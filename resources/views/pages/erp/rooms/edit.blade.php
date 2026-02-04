@extends('layout.erp.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Edit Room</h3>
            <div class="text-muted small">Update room details and operational status.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('room') }}" class="btn btn-outline-secondary">Back to Rooms</a>
            <a href="{{ url('room/occupancy') }}" class="btn btn-outline-secondary">Occupancy</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $houseStatus = $room->getRawOriginal('status') ?? 'available';
        $occupancy = $room->status;
        $today = \Carbon\Carbon::today();
        $activeStay = $room->bookingRooms->first(function ($br) use ($today) {
            return $br->check_in <= $today && $br->check_out > $today;
        });
        $nextStay = $room->bookingRooms
            ->filter(fn($br) => $br->check_in > $today)
            ->sortBy('check_in')
            ->first();
    @endphp

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Room Details</span>
                    <span class="text-muted small">Room ID: {{ $room->id }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ url('room/update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Room Number *</label>
                                <input type="text" name="room_number" class="form-control"
                                    value="{{ old('room_number', $room->room_number) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Room Type *</label>
                                <select name="room_type_id" class="form-select" required>
                                    <option value="" disabled hidden>Select a room type</option>
                                    @foreach ($roomTypes as $type)
                                        <option value="{{ $type->id }}"
                                            @if (old('room_type_id', $room->room_type_id) == $type->id) selected @endif>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Floor *</label>
                                <input type="number" name="floor" class="form-control"
                                    value="{{ old('floor', $room->floor) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">House Status *</label>
                                <select name="status" class="form-select" required>
                                    @php
                                        $selectedStatus = old('status', $houseStatus);
                                    @endphp
                                    <option value="available" @if($selectedStatus === 'available') selected @endif>Available</option>
                                    <option value="booked" @if($selectedStatus === 'booked') selected @endif>Booked</option>
                                    <option value="maintenance" @if($selectedStatus === 'maintenance') selected @endif>Maintenance</option>
                                </select>
                                <div class="text-muted small mt-1">Use Maintenance for out-of-service rooms.</div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ url('room') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-bold">Room Type Snapshot</div>
                <div class="card-body">
                    <div class="mb-2">
                        <div class="text-muted small">Type</div>
                        <div class="fw-bold">{{ $room->roomType->name ?? 'Unassigned' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Bed Type</div>
                        <div>{{ $room->roomType->bed_type ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Beds / Capacity</div>
                        <div>
                            {{ $room->roomType->bed_count ?? 'N/A' }} bed(s),
                            {{ $room->roomType->capacity ?? 'N/A' }} guest(s)
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Base Rate</div>
                        <div>{{ $room->roomType->price_per_night ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-bold">Current Occupancy</div>
                <div class="card-body">
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-secondary">House: {{ ucfirst(str_replace('_', ' ', $houseStatus)) }}</span>
                        @php
                            $occClass = match ($occupancy) {
                                'occupied' => 'bg-danger',
                                'booked' => 'bg-warning text-dark',
                                'available' => 'bg-success',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $occClass }}">Occupancy: {{ ucfirst($occupancy) }}</span>
                    </div>
                    @if ($activeStay)
                        <div class="fw-bold text-danger">In House</div>
                        <div class="text-muted small">{{ $activeStay->check_in }} to {{ $activeStay->check_out }}</div>
                    @elseif ($nextStay)
                        <div class="fw-bold text-warning">Next Stay</div>
                        <div class="text-muted small">{{ $nextStay->check_in }} to {{ $nextStay->check_out }}</div>
                    @else
                        <div class="text-muted">No upcoming stay</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">Audit</div>
                <div class="card-body">
                    <div class="text-muted small">Created</div>
                    <div class="mb-2">{{ $room->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</div>
                    <div class="text-muted small">Last Updated</div>
                    <div>{{ $room->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
