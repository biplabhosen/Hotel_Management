@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Bookings</h3>
    <a href="{{ url('booking/create') }}" class="btn btn-primary">+ New Booking</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search guest, phone or email">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @if(request('status')==$s) selected @endif>{{ ucfirst(str_replace('_',' ', $s)) }} @if(isset($counts[$s]) ) ({{ $counts[$s] }}) @endif</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary" type="submit">Filter</button>
                <a href="{{ url('booking') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>#</th>
                        <th>Guest</th>
                        <th>Rooms</th>
                        <th>Stay</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>
                                <div><strong>{{ $booking->guest->full_name ?? '—' }}</strong></div>
                                <small class="text-muted">{{ $booking->guest->phone ?? '' }} · {{ $booking->guest->email ?? '' }}</small>
                            </td>
                            <td style="min-width:180px">
                                @foreach ($booking->bookingRooms as $br)
                                    <div class="mb-1">
                                        <span class="badge bg-secondary">Room {{ $br->room->room_number ?? $br->room_id }}</span>
                                        <small class="text-muted ms-1">{{ $br->room->roomType->name ?? '' }}</small>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $booking->arrival ?? '—' }}</strong>
                                    <span class="text-muted">→</span>
                                    <strong>{{ $booking->departure ?? '—' }}</strong>
                                </div>
                                @php
                                    $nights = 0;
                                    foreach ($booking->bookingRooms as $br) {
                                        $ci = \Carbon\Carbon::parse($br->check_in);
                                        $co = \Carbon\Carbon::parse($br->check_out);
                                        $nights = max($nights, $ci->diffInDays($co));
                                    }
                                @endphp
                                <small class="text-muted">{{ $nights }} night(s)</small>
                            </td>
                            <td class="text-end">
                                <div><strong>{{ $booking->computed_total ?? '0.00' }}</strong></div>
                                <small class="text-success">Paid: {{ $booking->paid_amount ?? '0.00' }}</small>
                                <div><small class="text-danger">Due: {{ $booking->due_amount ?? '0.00' }}</small></div>
                            </td>
                            <td>
                                @php
                                    $sClass = match($booking->status) {
                                        'reserved' => 'badge bg-warning text-dark',
                                        'checked_in' => 'badge bg-success',
                                        'checked_out' => 'badge bg-secondary',
                                        'cancelled' => 'badge bg-danger',
                                        default => 'badge bg-light',
                                    };
                                @endphp
                                <span class="{{ $sClass }}">{{ ucfirst(str_replace('_',' ', $booking->status)) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $booking->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-primary">View</a>

                                    @if($booking->status === 'reserved' && $booking->arrival === now()->toDateString())
                                        <form method="POST" action="{{ url('booking/check-in/'.$booking->id) }}" onsubmit="return confirm('Confirm check-in for this booking?')">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">Check In</button>
                                        </form>
                                    @endif

                                    @if($booking->status === 'checked_in' && $booking->departure === now()->toDateString())
                                        <form method="POST" action="{{ url('booking/check-out/'.$booking->id) }}" onsubmit="return confirm('Confirm check-out for this booking?')">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" type="submit">Check Out</button>
                                        </form>
                                    @endif

                                    <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                Showing <strong>{{ $bookings->count() }}</strong> of <strong>{{ $bookings->total() }}</strong> bookings
            </div>
            <div>
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

