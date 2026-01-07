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

        .stat-purple { background: linear-gradient(135deg, #6f42c1, #8e63d9); }
        .stat-green { background: linear-gradient(135deg, #28a745, #5dd879); }
        .stat-orange { background: linear-gradient(135deg, #fd7e14, #ff9f43); }
        .stat-blue { background: linear-gradient(135deg, #0d6efd, #4dabf7); }

        /* Room Card */
        .room-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,.08);
        }

        .badge-status {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .status-available { background: #d4edda; color: #155724; }
        .status-occupied { background: #f8d7da; color: #721c24; }
        .status-booked { background: #fff3cd; color: #856404; }

        .amenities i {
            color: #28a745;
            margin-right: 5px;
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
        <div class="col-md-3">
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
        </div>

        <!-- AVAILABLE ROOM -->
        @foreach ($rooms as $room)
        <div class="col-md-3">
            <div class="room-card">
                <div class="d-flex justify-content-between mb-2">
                    <span>
                        <strong>Room {{  $room->room_number }}</strong><br>
                        {{-- <small >Floor {{ $room->floor  }} </small> --}}

                    </span>
                    <span class="badge-status status-{{ $room->status  }}">
                        <i class="bi bi-check-circle me-1"></i> {{ ucfirst($room->status)  }}
                    </span>
                </div>

                <small class="text-muted">{{ $room->roomType?->name  }}</small>

                <p class="mt-2 mb-1">${{ $room->roomType?->price_per_night  }} / night</p>
                <p class="mt-2 mb-1">{{ $room->roomType?->bed_type  }} Bed</p>

                <small>{{ $room->roomType?->capacity  }} Adults</small>

                <div class="amenities mt-2">
                    @foreach ($room->roomType->amenities as $amenity)
                    <i class="bi bi-{{$amenity->icon}}" title="{{$amenity->name}}" ></i>
                    @endforeach
                </div>

                <button class="btn btn-success btn-sm w-100 mt-3">
                    <i class="bi bi-plus-circle me-1"></i> Add Guest
                </button>
            </div>
        </div>
         @endforeach
    </div>
</div>


@endsection
