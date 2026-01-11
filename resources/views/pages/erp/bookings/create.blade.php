@extends('layout.erp.app')
{{-- @section('page_title', 'Create Booking')
@section('page_header', 'Create New Booking')
@section('page_breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('bookings') }}">Bookings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Booking</li>
        </ol>
    </nav>
@endsection --}}
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
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
    <form method="POST" action="{{ url('booking/store') }}">
        @csrf
        {{-- @session('success')
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endsession --}}
        <div class="card mb-3">
            <div class="card-header">Guest Information</div>
            <div class="card-body row g-3">

                <div class="col-md-4">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Phone *</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Booking Details</div>
            <div class="card-body row g-3">

                <div class="col-md-4">
                    <label class="form-label">Total Guests *</label>
                    <input type="number" name="total_guests" min="1" class="form-control" required>
                </div>

            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Room Selection</div>
            <div class="card-body row g-3">

                {{-- Room --}}
                <div class="col-md-4">
                    <label class="form-label">Room *</label>
                    <select name="rooms[0][room_id]" class="form-select" required>
                        <option value="">Select Room</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}">
                                Room {{ $room->room_number }} ({{ $room->roomType->name }})
                            </option>
                        @endforeach
                    </select>
                    {{-- <select id="room_type_id">
                        <option value="">Select Room Type</option>
                        @foreach ($roomTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select> --}}
                </div>

                {{-- Check In --}}
                <div class="col-md-4">
                    <label class="form-label">Check In *</label>
                    <input type="date" name="rooms[0][check_in]" class="form-control" required>
                </div>

                {{-- Check Out --}}
                <div class="col-md-4">
                    <label class="form-label">Check Out *</label>
                    <input type="date" name="rooms[0][check_out]" class="form-control" required>
                </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Create Booking
            </button>
        </div>

    </form>
@endsection
