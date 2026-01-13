@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0" style="font-weight: 600;">
            <i class="fas fa-coins text-success"></i> Record Payment
        </h2>
        <small class="text-muted">Booking #{{ $booking->id }} - {{ $booking->guest->full_name }}</small>
    </div>
    <a href="{{ route('booking.show', $booking) }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Guest Information Section -->
        <div class="card shadow-sm mb-4 border-start border-info" style="border-left: 4px solid #0D6EFF !important;">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 text-dark"><i class="fas fa-user-circle text-info"></i> Guest Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Guest Name</p>
                        <p class="mb-3 text-dark" style="font-weight: 600; font-size: 1.1rem;">{{ $booking->guest->full_name }}</p>
                        <p class="mb-1 text-muted small">Phone</p>
                        <p class="mb-0 text-dark"><i class="fas fa-phone text-info"></i> {{ $booking->guest->phone }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Email</p>
                        <p class="mb-3 text-dark"><i class="fas fa-envelope text-info"></i> {{ $booking->guest->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Check-in</p>
                        <p class="mb-2 text-dark"><i class="fas fa-calendar-check text-info"></i> {{ \Carbon\Carbon::parse($booking->arrival)->format('d M Y') }}</p>
                        <p class="mb-1 text-muted small">Check-out</p>
                        <p class="mb-0 text-dark"><i class="fas fa-calendar-times text-info"></i> {{ \Carbon\Carbon::parse($booking->departure)->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-gradient border-0" style="background: linear-gradient(135deg, #198754 0%, #1f8c5e 100%);">
                <h5 class="mb-0 text-white"><i class="fas fa-money-check-alt"></i> Payment Information</h5>
            </div>
            <div class="card-body">
                <!-- Amount Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded text-center border-0 shadow-sm">
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">Total Amount</p>
                            <h3 class="mb-0 text-primary" style="font-weight: 700; font-size: 1.8rem;">{{ number_format($total, 2) }}</h3>
                            <small class="text-muted" style="font-size: 0.85rem;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #155724 100%);">
                            <p class="text-white mb-2" style="font-size: 0.9rem; opacity: 0.9;">Already Paid</p>
                            <h3 class="mb-0 text-white" style="font-weight: 700; font-size: 1.8rem;">
                                <i class="fas fa-check-circle"></i> {{ number_format($paidAmount, 2) }}
                            </h3>
                            <small class="text-white" style="font-size: 0.85rem; opacity: 0.9;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);">
                            <p class="text-dark mb-2" style="font-size: 0.9rem; font-weight: 500;">Due Amount</p>
                            <h3 class="mb-0 text-dark" style="font-weight: 700; font-size: 1.8rem;">
                                <i class="fas fa-exclamation-triangle"></i> {{ number_format($dueAmount, 2) }}
                            </h3>
                            <small class="text-dark" style="font-size: 0.85rem; font-weight: 500;">BDT</small>
                        </div>
                    </div>
                </div>

                @if($dueAmount <= 0)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> This booking is fully paid. No additional payment is required.
                    </div>
                @else
                    <form action="{{ route('payment.store', $booking) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label"><strong>Payment Amount <span class="text-danger">*</span></strong></label>
                            <div class="input-group">
                                <input type="number" id="amount" name="amount" class="form-control form-control-lg" placeholder="0.00" step="0.01" min="0.01" max="{{ $dueAmount }}" required @error('amount') is-invalid @enderror value="{{ old('amount') }}">
                                <span class="input-group-text">BDT</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Maximum allowed: {{ number_format($dueAmount, 2) }} BDT</small>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="method" class="form-label"><strong>Payment Method <span class="text-danger">*</span></strong></label>
                                <select id="method" name="method" class="form-select form-select-lg" required @error('method') is-invalid @enderror>
                                    <option value="">Select payment method...</option>
                                    @foreach($methods as $m)
                                        <option value="{{ $m }}" @if(old('method') == $m) selected @endif>
                                            @if($m == 'cash')
                                                <i class="fas fa-money-bill"></i> Cash
                                            @elseif($m == 'card')
                                                <i class="fas fa-credit-card"></i> Card
                                            @elseif($m == 'mobile_banking')
                                                <i class="fas fa-mobile-alt"></i> Mobile Banking
                                            @else
                                                <i class="fas fa-bank"></i> Bank Transfer
                                            @endif
                                            {{ ucfirst(str_replace('_', ' ', $m)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('method')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label"><strong>Payment Type <span class="text-danger">*</span></strong></label>
                                <select id="type" name="type" class="form-select form-select-lg" required @error('type') is-invalid @enderror>
                                    <option value="">Select payment type...</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" @if(old('type') == $t) selected @endif>
                                            @if($t == 'advance')
                                                <i class="fas fa-hand-holding-usd"></i> Advance
                                            @elseif($t == 'balance')
                                                <i class="fas fa-balance-scale"></i> Balance
                                            @else
                                                <i class="fas fa-undo"></i> Refund
                                            @endif
                                            {{ ucfirst($t) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reference" class="form-label">Transaction Reference / Cheque Number</label>
                            <input type="text" id="reference" name="reference" class="form-control" placeholder="e.g., TXN123456, CHQ001" @error('reference') is-invalid @enderror value="{{ old('reference') }}">
                            @error('reference')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Optional: Enter transaction ID or cheque number for reference</small>
                        </div>

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-lg btn-success flex-grow-1">
                                <i class="fas fa-check"></i> Record Payment
                            </button>
                            <a href="{{ route('booking.show', $booking) }}" class="btn btn-lg btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Booking Details Card -->
        <div class="card shadow-sm mb-4 border-top border-primary" style="border-top: 3px solid #0D6EFF !important;">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-info-circle text-primary"></i> Booking Summary</h6>
            </div>
            <div class="card-body">
                <div class="p-3 bg-primary bg-opacity-10 rounded mb-3 border border-primary">
                    <p class="text-muted small mb-1">Booking ID</p>
                    <p class="h6 mb-0 text-dark"><strong>#{{ $booking->id }}</strong></p>
                </div>

                <p class="mb-2 text-muted small">Check-in</p>
                <p class="mb-3 text-dark small"><i class="fas fa-calendar-check text-success"></i> {{ \Carbon\Carbon::parse($booking->arrival)->format('d M Y') }}</p>

                <p class="mb-2 text-muted small">Check-out</p>
                <p class="mb-3 text-dark small"><i class="fas fa-calendar-times text-danger"></i> {{ \Carbon\Carbon::parse($booking->departure)->format('d M Y') }}</p>

                <hr>

                <p class="mb-2 text-muted small">Total Rooms</p>
                <p class="mb-3 text-dark"><strong>{{ $booking->bookingRooms->count() }}</strong> Room(s)</p>

                <p class="mb-2 text-muted small">Total Guests</p>
                <p class="mb-0 text-dark"><strong>{{ $booking->total_guests }}</strong> Guest(s)</p>
            </div>
        </div>

        <!-- Rooms Details Card -->
        <div class="card shadow-sm mb-4 border-start border-success" style="border-left: 4px solid #198754 !important;">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-door-open text-success"></i> Rooms Booked</h6>
            </div>
            <div class="card-body">
                @forelse($booking->bookingRooms as $br)
                    <div class="mb-3 pb-3 border-bottom" style="border-bottom: 1px solid #e9ecef;">
                        <p class="mb-1"><strong class="text-primary">{{ $br->room->room_number }}</strong>
                            <span class="badge bg-info ms-2">{{ $br->room->roomType->name ?? 'Standard' }}</span>
                        </p>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($br->check_in)->format('d M') }} -
                            {{ \Carbon\Carbon::parse($br->check_out)->format('d M Y') }}
                        </p>
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-moon text-warning"></i> {{ max(1, \Carbon\Carbon::parse($br->check_in)->diffInDays($br->check_out)) }} nights ×
                            <strong class="text-success">{{ number_format($br->price_per_night, 2) }} BDT</strong>/night
                        </p>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No rooms assigned</p>
                @endforelse
            </div>
        </div>

        <!-- Payment Guidelines Card -->
        <div class="card shadow-sm border-start border-danger" style="border-left: 4px solid #DC3545 !important;">
            <div class="card-header bg-danger bg-opacity-10 border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-shield-alt text-danger"></i> Payment Rules</h6>
            </div>
            <div class="card-body small">
                <ul class="list-unstyled">
                    <li class="mb-2 p-2 bg-success bg-opacity-10 rounded">
                        <i class="fas fa-check-circle text-success"></i> <strong class="text-success">50%</strong> advance required for <strong>check-in</strong>
                    </li>
                    <li class="mb-2 p-2 bg-danger bg-opacity-10 rounded">
                        <i class="fas fa-check-double text-danger"></i> <strong class="text-danger">100%</strong> payment required for <strong>check-out</strong>
                    </li>
                    <li class="mb-2 p-2 bg-info bg-opacity-10 rounded">
                        <i class="fas fa-undo text-info"></i> Refunds available for <strong>paid</strong> payments
                    </li>
                    <li class="p-2 bg-warning bg-opacity-10 rounded">
                        <i class="fas fa-file-alt text-warning"></i> Keep transaction <strong>reference</strong> for audit trail
                    </li>
                </ul>
            </div>
        </div>
@endsection
