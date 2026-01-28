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
        {{-- <div class="card mb-3">
            <div class="card-header">Room Selection</div>
            <div class="card-body row g-3">


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
                </div>


                <div class="col-md-4">
                    <label class="form-label">Check In *</label>
                    <input type="date" name="rooms[0][check_in]" class="form-control" required>
                </div>


                <div class="col-md-4">
                    <label class="form-label">Check Out *</label>
                    <input type="date" name="rooms[0][check_out]" class="form-control" required>
                </div>
            </div>
        </div> --}}
        <div class="mb-3">
            <label class="form-label">Room Type</label>
            <select id="room_type_id" class="form-select">
                <option value="">Select Room Type</option>

                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}">
                        {{ $type->name }} ({{ $type->capacity }} Guests)
                    </option>
                @endforeach
            </select>
            <div class="col-md-4">
                <label class="form-label">Check In *</label>
                <input type="date" id="check_in" name="check_in" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Check Out *</label>
                <input type="date" id="check_out" name="check_out" class="form-control" required>
            </div>
        </div>
        {{-- <div id="available_rooms_section" class="mb-3" style="display: none;">
            <h5>Available Rooms</h5>
            <div id="available-rooms" class="row g-3"></div>
                <!-- Available rooms will be displayed here -->
        </div> --}}
        {{-- <div id="available-rooms" class="row mt-3"></div> --}}
        <div id="rooms-section" class="mt-3">
            <div id="loading-spinner" class="text-center d-none">
                <div class="spinner-border" role="status"></div>
                <p class="mt-2">Checking availability...</p>
            </div>

            <div id="no-rooms" class="alert alert-warning d-none">
                No rooms available for the selected dates.
            </div>

            <div id="available-rooms" class="row"></div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Create Booking
            </button>
        </div>

    </form>
@endsection
print_r($request->all())
@section('js')
    {{-- <script>
        $(document).ready(function() {
            $('#check_availability').click(function() {
                var roomTypeId = $('#room_type_id').val();
                var checkIn = $('#check_in').val();
                var checkOut = $('#check_out').val();

                if (!roomTypeId || !checkIn || !checkOut) {
                    alert('Please select room type, check-in and check-out dates.');
                    return;
                }

                $.ajax({
                    url: '{{ url("booking/available") }}',
                    method: 'GET',
                    data: {
                        room_type_id: roomTypeId,
                        check_in: checkIn,
                        check_out: checkOut
                    },
                    success: function(response) {
                        var roomsList = $('#available_rooms_list');
                        roomsList.empty();

                        if (response.length > 0) {
                            response.forEach(function(room) {
                                var roomCard = `
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Room ${room.room_number}</h5>
                                                <p class="card-text">Type: ${room.room_type.name}</p>
                                                <button type="button" class="btn btn-primary select-room-btn" data-room-id="${room.id}">Select Room</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                roomsList.append(roomCard);
                            });
                            $('#available_rooms_section').show();
                        } else {
                            roomsList.append('<p>No rooms available for the selected criteria.</p>');
                            $('#available_rooms_section').show();
                        }
                    },
                    error: function() {
                        alert('An error occurred while fetching available rooms.');
                    }
                });
            });
            $(document).on('click', '.select-room-btn', function() {
                var roomId = $(this).data('room-id');
                var checkIn = $('#check_in').val();
                var checkOut = $('#check_out').val();

                // Create hidden inputs to include selected room details in the form
                var roomInput = `
                    <input type="hidden" name="rooms[][room_id]" value="${roomId}">
                    <input type="hidden" name="rooms[][check_in]" value="${checkIn}">
                    <input type="hidden" name="rooms[][check_out]" value="${checkOut}">
                `;
                $('form').append(roomInput);
                alert('Room ' + roomId + ' selected for booking.');
            });
        });
    </script> --}}
    <script>
        function loadAvailableRooms() {
            const roomType = document.getElementById('room_type_id').value;
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;

            if (!roomType || !checkIn || !checkOut) return;

            const spinner = document.getElementById('loading-spinner');
            const noRooms = document.getElementById('no-rooms');
            const container = document.getElementById('available-rooms');

            spinner.classList.remove('d-none');
            noRooms.classList.add('d-none');
            container.innerHTML = '';

            fetch(`{{URL("/")}}/booking/available?room_type_id=${roomType}&check_in=${checkIn}&check_out=${checkOut}`)
                .then(res => res.json())
                .then(rooms => {
                    spinner.classList.add('d-none');

                    if (rooms.length === 0) {
                        noRooms.classList.remove('d-none');
                        return;
                    }

                    let html = '';
                    rooms.forEach(room => {
                        html += `
                    <div class="col-md-3 mb-3">
                        <label class="card p-3 h-100 cursor-pointer">
                            <input type="checkbox" name="rooms[]" value="${room.id}">
                            <strong>Room ${room.room_number}</strong><br>
                            <small>${room.room_type.name}</small>
                        </label>
                    </div>
                `;
                    });

                    container.innerHTML = html;
                })
                .catch(() => {
                    spinner.classList.add('d-none');
                    alert('Failed to load rooms');
                });
        }

        ['room_type_id', 'check_in', 'check_out'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', loadAvailableRooms);
        });
    </script>
@endsection
