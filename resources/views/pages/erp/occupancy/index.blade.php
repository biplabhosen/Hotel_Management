@extends('layout.erp.app')

@section('content')
    @php
        // print_r($rooms)
    @endphp
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        /* Summary Cards */
        .stat-card {
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            min-height: 110px;
        }

        .stat-purple {
            background: linear-gradient(135deg, #6f42c1, #8e63d9);
        }

        .stat-green {
            background: linear-gradient(135deg, #28a745, #5dd879);
        }

        .stat-orange {
            background: linear-gradient(135deg, #fd7e14, #ff9f43);
        }

        .stat-blue {
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
        }

        /* Room Card */

        .room-card {
            background: #fbfbfb;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            padding: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .room-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .badge-status {
            display: inline-flex;
            /* IMPORTANT */
            align-items: center;
            gap: 6px;

            padding: 6px 14px;
            /* proper badge padding */
            font-size: 0.75rem;
            font-weight: 500;

            border-radius: 999px;
            /* pill shape */
            white-space: nowrap;
            /* prevent shrink */
            flex-shrink: 0;
            /* IMPORTANT */

            line-height: 1;
        }

        .status-available {
            background: #e6f7ee;
            color: #198754;
        }

        .status-occupied {
            background: #fdecea;
            color: #dc3545;
        }

        .status-booked {
            background: #fff3cd;
            color: #ffc107;
        }

        .status-icons span {
            font-size: 0.7rem;
        }

        .room-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }


        .amenities i {
            font-size: 1.1rem;
            color: #28a745;
            margin-right: 5px;
        }

        .amenities i:hover {
            color: #0d6efd;
        }
    </style>

    <div class="container-fluid p-4">

        <!-- SUMMARY -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-purple d-flex justify-content-between">
                    <div>
                        <h3>20</h3>
                        <small>Total Rooms</small>
                    </div>
                    <i class="bi bi-building fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-green d-flex justify-content-between">
                    <div>
                        <h3>10</h3>
                        <small>Available</small>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-orange d-flex justify-content-between">
                    <div>
                        <h3>7</h3>
                        <small>Occupied</small>
                    </div>
                    <i class="bi bi-door-closed fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue d-flex justify-content-between">
                    <div>
                        <h3>35%</h3>
                        <small>Occupancy Rate</small>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-funnel me-1"></i> Room Filters
                </h6>

                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input class="form-control" placeholder="Search room">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select">
                            <option>Status</option>
                            <option>Available</option>
                            <option>Occupied</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select class="form-select">
                            <option>Room Type</option>
                            <option>Deluxe</option>
                            <option>Suite</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROOMS -->
        <div class="row g-4">

            <!-- ROOM CARD -->
            {{-- <div class="col-md-3">
            <div class="room-card">
                <div class="d-flex justify-content-between mb-2">
                    <strong>
                        <i class="bi bi-door-open me-1"></i> Room 101
                    </strong>
                    <span class="badge-status status-occupied">
                        <i class="bi bi-person-fill-lock me-1"></i> Occupied
                    </span>
                </div>

                <small class="text-muted">
                    <i class="bi bi-star-fill me-1"></i> Super Deluxe
                </small>

                <p class="mt-2 mb-1">
                    <i class="bi bi-currency-dollar"></i> 320 / night
                </p>

                <small>
                    <i class="bi bi-people"></i> 2 Adults, 2 Children
                </small>

                <div class="amenities mt-2">
                    <i class="bi bi-wifi"></i>
                    <i class="bi bi-tv"></i>
                    <i class="bi bi-cup-hot"></i>
                    <i class="bi bi-snow"></i>
                </div>

                <button class="btn btn-primary btn-sm w-100 mt-3">
                    <i class="bi bi-person-vcard me-1"></i> Guest Details
                </button>
            </div>
        </div> --}}

            <!-- AVAILABLE ROOM -->
            @foreach ($rooms as $room)
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
                            <span class="badge-status status-{{ $room->status }}">
                                @if ($room->status === 'available')
                                    <i class="bi bi-check-circle me-1"></i>
                                @elseif ($room->status === 'occupied')
                                    <i class="bi bi-person-fill-lock me-1"></i>
                                @elseif ($room->status === 'booked')
                                    <i class="bi bi-calendar-check me-1"></i>
                                @endif
                                {{ ucfirst($room->status) }}
                            </span>
                        </div>

                        <!-- Status Icons -->
                        {{-- <div class="d-flex gap-3 text-muted mb-2 status-icons">
            <span><i class="bi bi-check-circle"></i> Available</span>
            <span><i class="bi bi-calendar-check"></i> Booked</span>
            <span><i class="bi bi-person-fill-lock"></i> Occupied</span>
        </div> --}}

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
                                <i class="bi bi-{{ $amenity->icon }}" data-bs-toggle="tooltip"
                                    title="{{ $amenity->name }}"></i>
                            @endforeach
                        </div>

                        <!-- Status-wise Button -->
                        <div class="mt-3">
                            @if ($room->status === 'available')
                                <button class="btn btn-success btn-sm w-100" data-bs-toggle="offcanvas"
                                    data-bs-target="#guestOffcanvas">
                                    <i class="bi bi-person-plus me-1"></i> Add Guest
                                </button>
                            @elseif ($room->status === 'occupied')
                                <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal"
                                    data-bs-target="#guestModal">
                                    <i class="bi bi-person-vcard me-1"></i> View Guest
                                </button>
                            @elseif ($room->status === 'booked')
                                <button class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-calendar-plus me-1"></i> Book Now
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection
<div class="modal fade" id="guestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-vcard me-1"></i> Guest Details
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> John Doe</p>
                <p><strong>Phone:</strong> +8801XXXXXXXXX</p>
                <p><strong>Check-in:</strong> 12 Jan 2026</p>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" id="guestOffcanvas">
    <div class="offcanvas-header">
        <h5>
            <i class="bi bi-person-plus me-1"></i> Add Guest
        </h5>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form>
            <div class="mb-3">
                <label class="form-label">Guest Name</label>
                <input type="text" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control">
            </div>

            <button class="btn btn-success w-100">
                <i class="bi bi-check-circle me-1"></i> Confirm Check-in
            </button>
        </form>
    </div>
</div>
