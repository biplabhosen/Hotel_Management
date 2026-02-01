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
                        <h3>{{ $totalRooms ?? 0 }}</h3>
                        <small>Total Rooms</small>
                    </div>
                    <i class="bi bi-building fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-green d-flex justify-content-between">
                    <div>
                        <h3>{{ $availableCount ?? 0 }}</h3>
                        <small>Available</small>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-orange d-flex justify-content-between">
                    <div>
                        <h3>{{ $occupiedCount ?? 0 }}</h3>
                        <small>Occupied</small>
                    </div>
                    <i class="bi bi-door-closed fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-blue d-flex justify-content-between">
                    <div>
                        <h3>{{ $occupancyRate ?? 0 }}%</h3>
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

                <form method="GET" action="{{ url('room/occupancy') }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search room">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Status</option>
                                <option value="available" {{ request('status')=='available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ request('status')=='occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="booked" {{ request('status')=='booked' ? 'selected' : '' }}>Booked</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="room_type" class="form-select">
                                <option value="">All Room Types</option>
                                @foreach($roomTypes as $rt)
                                    <option value="{{ $rt->id }}" {{ request('room_type') == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>

                        <div class="col-md-1 d-flex gap-1">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ url('room/occupancy') }}" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ROOMS -->
        <div id="rooms-list" class="row g-4">
            @include('pages.erp.occupancy.partials.rooms', ['roomsWithStatus' => $roomsWithStatus])
        </div>
                    </div>
                </div>
            {{-- @endforeach --}}

        </div>
    </div>
    {{-- Modal --}}
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
@endsection

    @section('js')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action="{{ url('room/occupancy') }}"]');
        const roomsList = document.getElementById('rooms-list');
        const debounce = (fn, delay = 300) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        };

        async function submitAjax() {
            const params = new URLSearchParams(new FormData(form));
            const url = '{{ url('room/occupancy/ajax') }}?' + params.toString();
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.roomsHtml) {
                roomsList.innerHTML = data.roomsHtml;
            }
            // update summary cards
            document.querySelectorAll('.stat-card').forEach(card => {
                // map by small text within card
                const small = card.querySelector('small')?.innerText?.trim();
                if (small === 'Total Rooms') card.querySelector('h3').innerText = data.totalRooms;
                if (small === 'Available') card.querySelector('h3').innerText = data.availableCount;
                if (small === 'Occupied') card.querySelector('h3').innerText = data.occupiedCount;
                if (small === 'Occupancy Rate') card.querySelector('h3').innerText = data.occupancyRate + '%';
            });
        }

        // submit on form submit
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            submitAjax();
            history.replaceState({}, '', this.action + '?' + new URLSearchParams(new FormData(this)).toString());
        });

        // live inputs
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(i => i.addEventListener('input', debounce(submitAjax)));

    });
    </script>
    @endsection


