@extends('layout.erp.app')

@section('content')
<div class="row mb-3">
    <div class="col-sm-12">
        <h4 class="mb-0">Booking Checkout</h4>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Guest</th>
                        <th>Rooms</th>
                        <th>Stay</th>
                        <th>Status</th>
                        <th style="width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->guest->full_name ?? 'N/A' }}</td>
                            <td>
                                @foreach ($booking->bookingRooms as $bookingRoom)
                                    <span class="badge badge-info mr-1 mb-1">
                                        Room {{ $bookingRoom->room->room_number ?? 'N/A' }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                {{ optional($booking->bookingRooms->min('check_in'))->format('Y-m-d') ?? '-' }}
                                to
                                {{ optional($booking->bookingRooms->max('check_out'))->format('Y-m-d') ?? '-' }}
                            </td>
                            <td><span class="badge badge-warning">{{ $booking->status }}</span></td>
                            <td>
                                <form action="{{ route('booking.checkout.submit', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        Check Out
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No checked-in bookings available for checkout.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
