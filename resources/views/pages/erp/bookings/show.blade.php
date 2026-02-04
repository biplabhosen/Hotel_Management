@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-0">Booking #{{ $booking->id }} <small class="text-muted">· {{ $booking->status }}</small></h3>
        <small class="text-muted">Created: {{ optional($booking->created_at)->format('d M Y H:i') }}</small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ url('booking') }}" class="btn btn-outline-secondary"><i class="fe fe-arrow-left"></i> Back</a>
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fe fe-printer"></i> Print</button>
        @if($booking->status === 'reserved')
            <form action="{{ route('booking.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel booking?')">
                @csrf
                <button class="btn btn-danger">Cancel Booking</button>
            </form>
        @endif
        <a href="{{ route('payment.create', $booking) }}" class="btn btn-warning"><i class="fe fe-credit-card"></i> Record Payment</a>
        @if($booking->status === 'checked_out')
            <a href="{{ route('payment.invoice', $booking) }}" class="btn btn-primary"><i class="fe fe-file-text"></i> Generate Invoice</a>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Guest Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>{{ $booking->guest->full_name ?? '—' }}</strong></p>
                        <p class="mb-1 text-muted"><i class="fe fe-phone me-1"></i> {{ $booking->guest->phone ?? '—' }}</p>
                        <p class="mb-0 text-muted"><i class="fe fe-mail me-1"></i> {{ $booking->guest->email ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Stay</strong></p>
                        <p class="mb-0"><i class="fe fe-calendar me-1"></i> {{ optional($booking->arrival)->format('d M Y') ?? $booking->arrival }} → {{ optional($booking->departure)->format('d M Y') ?? $booking->departure }}</p>
                        <p class="mb-0 text-muted">Nights: {{ $booking->nights ?? $booking->bookingRooms->first() ? $booking->bookingRooms->first()->check_in->diffInDays($booking->bookingRooms->first()->check_out) : '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Rooms</div>
            <div class="card-body">
                @foreach($booking->bookingRooms as $br)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Room {{ $br->room->room_number ?? $br->room_id }}</strong>
                            <div class="text-muted small">Type: {{ $br->room->roomType->name ?? '-' }} · {{ $br->room->roomType->capacity ?? '-' }} guests</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">{{ optional($br->check_in)->format('d M Y') ?? $br->check_in }} → {{ optional($br->check_out)->format('d M Y') ?? $br->check_out }}</div>
                            <div class="small">Price: <strong>{{ number_format($br->rate ?? $br->price ?? 0, 2) }}</strong></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Notes</div>
            <div class="card-body">
                <p class="mb-0">{{ $booking->notes ?? 'No notes.' }}</p>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Summary</div>
            <div class="card-body">
                <p class="mb-1"><strong>Total</strong></p>
                <p class="h4">{{ number_format($booking->computed_total ?? $booking->total_amount ?? 0, 2) }}</p>

                <p class="mb-1 text-success">Paid: {{ number_format($booking->paid_amount ?? 0, 2) }}</p>
                <p class="mb-1 text-danger">Due: {{ number_format($booking->due_amount ?? 0, 2) }}</p>

                <hr>
                <p class="mb-1"><small class="text-muted">Payments</small></p>
                @if($booking->payments && $booking->payments->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($booking->payments as $p)
                            <li class="mb-2">
                                <div class="d-flex justify-content-between small">
                                    <div>{{ optional($p->created_at)->format('d M Y') }} · {{ $p->method ?? '—' }}</div>
                                    <div><strong>{{ number_format($p->amount,2) }}</strong></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No payments recorded.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Status</div>
            <div class="card-body">
                <p class="mb-0">@php
                    $sClass = match($booking->status) {
                        'reserved' => 'badge bg-warning text-dark',
                        'checked_in' => 'badge bg-success',
                        'checked_out' => 'badge bg-secondary',
                        'cancelled' => 'badge bg-danger',
                        'no_show' => 'badge bg-dark',
                        default => 'badge bg-light',
                    };
                @endphp
                <span class="{{ $sClass }}">{{ ucfirst(str_replace('_',' ', $booking->status)) }}</span></p>
            </div>
        </div>

    </div>
</div>

@endsection
