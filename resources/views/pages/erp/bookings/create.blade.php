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
        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div><i class="fe fe-user me-2"></i> Guest Information</div>
                        <small class="text-muted">Quick add or select existing guest</small>
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Guests *</label>
                            <input type="number" name="total_guests" min="1" class="form-control" required>
                        </div>

                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="fe fe-calendar me-2"></i> Booking Dates & Type
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Check In *</label>
                            <input type="date" id="check_in" name="check_in" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Check Out *</label>
                            <input type="date" id="check_out" name="check_out" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Room Type</label>
                            <select id="room_type_id" name="room_type_id" class="form-select">
                                <option value="">Select Room Type</option>
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->capacity }} Guests)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <small class="text-muted">Select available rooms below. Selected rooms appear on the right summary.</small>
                        </div>
                    </div>
                </div>

                <div id="rooms-section" class="mb-3">
                    <div id="loading-spinner" class="text-center d-none">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Checking availability...</p>
                    </div>

                    <div id="no-rooms" class="alert alert-warning d-none">
                        No rooms available for the selected dates.
                    </div>

                    <div id="available-rooms" class="row"></div>
                </div>

            </div>

            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div><i class="fe fe-layers me-2"></i> Selected Rooms</div>
                        <small class="text-muted">Summary</small>
                    </div>
                    <div class="card-body">
                        <div id="selected-rooms-list">
                            <p class="text-muted">No rooms selected.</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('booking') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary d-flex gap-2 align-items-center">
                                <i class="fe fe-save"></i>
                                <span>Create Booking</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 d-none d-md-block">
                    <div class="card-body">
                        <h6 class="mb-1">Tips</h6>
                        <small class="text-muted">Use room type and dates to filter availability. Selected rooms will be reserved for the chosen dates.</small>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection
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

            // set minimum checkout to be next day of check-in
            try {
                const ci = new Date(checkIn);
                const minCo = new Date(ci);
                minCo.setDate(ci.getDate() + 1);
                document.getElementById('check_out').min = minCo.toISOString().slice(0,10);
            } catch (e) {}

            const spinner = document.getElementById('loading-spinner');
            const noRooms = document.getElementById('no-rooms');
            const container = document.getElementById('available-rooms');

            // clear previous selections when reloading availability
            document.querySelectorAll('[data-room-id-hidden]').forEach(el => el.remove());
            const selList = document.getElementById('selected-rooms-list');
            if(selList) selList.innerHTML = '<p class="text-muted">No rooms selected.</p>';

            spinner.classList.remove('d-none');
            noRooms.classList.add('d-none');
            container.innerHTML = '';

            fetch(`{{ url('/') }}/booking/available?room_type_id=${roomType}&check_in=${checkIn}&check_out=${checkOut}`)
                .then(res => res.json())
                .then(rooms => {
                    spinner.classList.add('d-none');

                    if (rooms.length === 0) {
                        noRooms.classList.remove('d-none');
                        return;
                    }

                    let html = '';
                    rooms.forEach(room => {
                        const price = room.room_type.price_per_night ? (parseFloat(room.room_type.price_per_night).toFixed(2)) : '—';
                        html += `
                    <div class="col-md-6 mb-3">
                        <div class="card room-card h-100" data-room-id="${room.id}">
                            <div class="card-body d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Room ${room.room_number}</h6>
                                    <div class="small text-muted">${room.room_type.name} · ${room.room_type.capacity || '—'} guests</div>
                                </div>
                                <div class="text-end">
                                    <div class="mb-2"><strong>BDT ${price}</strong></div>
                                    <div>
                                        <input type="checkbox" id="room-checkbox-${room.id}" class="select-room-checkbox d-none" data-room-id="${room.id}" data-room-number="${room.room_number}" data-room-type="${room.room_type.name}" />
                                        <label for="room-checkbox-${room.id}" class="btn btn-sm btn-outline-primary btn-select-room">Select</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    });

                    container.innerHTML = html;

                    // attach listeners
                    document.querySelectorAll('.select-room-checkbox').forEach(cb => {
                        cb.addEventListener('change', function(){
                            const id = this.dataset.roomId;
                            const number = this.dataset.roomNumber;
                            const type = this.dataset.roomType;

                            // find card
                            const card = document.querySelector('.room-card[data-room-id="'+id+'"]');
                            if(this.checked){
                                // add single hidden input rooms[] with id (avoid duplicates)
                                const form = document.querySelector('form');
                                if(!document.querySelector('[data-room-id-hidden="'+id+'"]')){
                                    const inp = document.createElement('input'); inp.type='hidden'; inp.name='rooms[]'; inp.value = id; inp.dataset.roomIdHidden = id;
                                    form.appendChild(inp);
                                }

                                card.classList.add('border-2','border-primary','bg-light');
                                addSelectedRoomToList(id, number, type);
                                // change label to 'Selected'
                                const lbl = card.querySelector('.btn-select-room'); if(lbl){ lbl.classList.remove('btn-outline-primary'); lbl.classList.add('btn-primary'); lbl.textContent = 'Selected'; }

                            } else {
                                // remove hidden input
                                document.querySelectorAll('[data-room-id-hidden="'+id+'"]').forEach(el => el.remove());
                                card.classList.remove('border-2','border-primary','bg-light');
                                removeSelectedRoomFromList(id);
                                const lbl = card.querySelector('.btn-select-room'); if(lbl){ lbl.classList.remove('btn-primary'); lbl.classList.add('btn-outline-primary'); lbl.textContent = 'Select'; }
                            }

                            updateSelectedRoomsSummary();
                        });
                    });

                    // make whole card clickable as well
                    document.querySelectorAll('.room-card').forEach(rc => {
                        rc.addEventListener('click', function(e){
                            // don't toggle if clicking the actual button (label) - allow label to handle checkbox
                            if(e.target.closest('label') || e.target.tagName === 'LABEL' || e.target.closest('input')) return;
                            const id = this.dataset.roomId;
                            const cb = document.getElementById('room-checkbox-' + id);
                            if(cb){ cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
                        });
                    });

                    // repopulate old selections if server returned previous rooms (e.g., validation failed)
                    try {
                        const oldSelected = @json(old('rooms', []));
                        if (oldSelected && Array.isArray(oldSelected) && oldSelected.length) {
                            oldSelected.forEach(id => {
                                const cb = document.getElementById('room-checkbox-' + id);
                                if(cb && !cb.checked){ cb.checked = true; cb.dispatchEvent(new Event('change')); }
                            });
                        }
                    } catch(e) { /* noop */ }

                })
                .catch(() => {
                    spinner.classList.add('d-none');
                    alert('Failed to load rooms');
                });
        }

        function addSelectedRoomToList(id, number, type){
            const list = document.getElementById('selected-rooms-list');
            if(!list) return;
            const container = document.createElement('div');
            container.className = 'selected-room mb-2';
            container.dataset.roomId = id;
            container.innerHTML = `<div class="d-flex justify-content-between align-items-center"><div><strong>Room ${number}</strong><div class="small text-muted">${type}</div></div><div><button type="button" class="btn btn-sm btn-outline-danger btn-remove-room" data-room-id="${id}">Remove</button></div></div>`;
            list.querySelector('p')?.remove();
            list.appendChild(container);

            container.querySelector('.btn-remove-room').addEventListener('click', function(){
                const rid = this.dataset.roomId;
                // uncheck checkbox
                const cb = document.querySelector('.select-room-checkbox[data-room-id="'+rid+'"]');
                if(cb) cb.checked = false;
                // remove hidden inputs
                document.querySelectorAll('[data-room-id-hidden="'+rid+'"]').forEach(el => el.remove());
                removeSelectedRoomFromList(rid);
                updateSelectedRoomsSummary();
            });
        }

        function removeSelectedRoomFromList(id){
            const list = document.getElementById('selected-rooms-list');
            const el = list.querySelector('.selected-room[data-room-id="'+id+'"]');
            if(el) el.remove();
            if(list.children.length === 0) list.innerHTML = '<p class="text-muted">No rooms selected.</p>';
        }

        function updateSelectedRoomsSummary(){
            const list = document.getElementById('selected-rooms-list');
            const count = list.querySelectorAll('.selected-room').length;
            // optionally show total or other summary
        }

        ['room_type_id', 'check_in', 'check_out'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', loadAvailableRooms);
        });
    </script>
@endsection
