@extends('layout.erp.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Rooms</h3>
            <div class="text-muted small">Inventory overview, live occupancy, and house status.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('room/occupancy') }}" class="btn btn-outline-secondary">Occupancy</a>
            <a href="{{ url('room/create') }}" class="btn btn-primary">+ Add Room</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Rooms</div>
                    <div class="fs-4 fw-bold">{{ $totalRooms }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Available</div>
                    <div class="fs-4 fw-bold text-success">{{ $availableCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Occupied</div>
                    <div class="fs-4 fw-bold text-danger">{{ $occupiedCount }}</div>
                    <div class="text-muted small">Occupancy {{ $occupancyRate }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Booked / Maintenance</div>
                    <div class="fs-4 fw-bold text-warning">{{ $bookedCount }}</div>
                    <div class="text-muted small">Maintenance {{ $maintenanceCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Search</label>
                    <input name="q" type="text" class="form-control" value="{{ request('q') }}"
                        placeholder="Room number or floor">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Room Type</label>
                    <select name="room_type" class="form-select">
                        <option value="">All types</option>
                        @foreach ($roomTypes as $type)
                            <option value="{{ $type->id }}" @if(request('room_type') == $type->id) selected @endif>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Occupancy</label>
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="available" @if(request('status') === 'available') selected @endif>Available</option>
                        <option value="booked" @if(request('status') === 'booked') selected @endif>Booked</option>
                        <option value="occupied" @if(request('status') === 'occupied') selected @endif>Occupied</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                    <a href="{{ url('room') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Occupancy</th>
                            <th>House Status</th>
                            <th>Stay Window</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roomsTableBody">
                        @forelse ($rooms as $room)
                            @php
                                $houseStatus = $room->getRawOriginal('status') ?? 'available';
                                $occupancy = $room->status;
                                $houseClass = match ($houseStatus) {
                                    'maintenance' => 'badge bg-danger',
                                    'available' => 'badge bg-success',
                                    default => 'badge bg-secondary',
                                };
                                $occClass = match ($occupancy) {
                                    'occupied' => 'badge bg-danger',
                                    'booked' => 'badge bg-warning text-dark',
                                    'available' => 'badge bg-success',
                                    default => 'badge bg-secondary',
                                };
                                $today = \Carbon\Carbon::today();
                                $activeStay = $room->bookingRooms->first(function ($br) use ($today) {
                                    return $br->check_in <= $today && $br->check_out > $today;
                                });
                                $nextStay = $room->bookingRooms
                                    ->filter(fn($br) => $br->check_in > $today)
                                    ->sortBy('check_in')
                                    ->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $room->room_number }}</div>
                                    <small class="text-muted">Room ID: {{ $room->id }}</small>
                                </td>
                                <td>
                                    <div>{{ $room->roomType->name ?? 'Unassigned' }}</div>
                                    <small class="text-muted">
                                        {{ $room->roomType->bed_type ?? 'N/A' }}
                                        @if(!empty($room->roomType?->bed_count))
                                            - {{ $room->roomType->bed_count }} bed(s)
                                        @endif
                                    </small>
                                </td>
                                <td>{{ $room->floor }}</td>
                                <td><span class="{{ $occClass }}">{{ ucfirst($occupancy) }}</span></td>
                                <td><span class="{{ $houseClass }}">{{ ucfirst(str_replace('_', ' ', $houseStatus)) }}</span></td>
                                <td>
                                    @if ($activeStay)
                                        <div class="fw-bold text-danger">In House</div>
                                        <small class="text-muted">{{ $activeStay->check_in }} to {{ $activeStay->check_out }}</small>
                                    @elseif ($nextStay)
                                        <div class="fw-bold text-warning">Next Stay</div>
                                        <small class="text-muted">{{ $nextStay->check_in }} to {{ $nextStay->check_out }}</small>
                                    @else
                                        <span class="text-muted">No upcoming stay</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ url('room/edit', $room->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="{{ url('booking/create') }}?room_id={{ $room->id }}" class="btn btn-sm btn-outline-secondary">New Booking</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">No rooms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    Showing <strong>{{ $rooms->count() }}</strong> of <strong>{{ $rooms->total() }}</strong> room(s)
                </div>
                <div>
                    {{ $rooms->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
