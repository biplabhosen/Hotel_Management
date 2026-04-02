@extends('layout.erp.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0" style="font-weight: 600;">
            <i class="fas fa-credit-card text-primary"></i> Pay Online
        </h2>
        <small class="text-muted">Booking #{{ $booking->id }} — {{ $booking->guest->full_name }}</small>
    </div>
    <a href="{{ route('booking.show', $booking) }}" class="btn btn-outline-secondary">← Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Guest Information --}}
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

        {{-- SSLCommerz Payment Form --}}
        <div class="card shadow-sm">
            <div class="card-header bg-gradient border-0" style="background: linear-gradient(135deg, #0D6EFF 0%, #0052CC 100%);">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-shield-alt"></i> Secure Online Payment
                    <span class="badge bg-light text-primary ms-2" style="font-size: 0.65rem;">SSLCommerz</span>
                </h5>
            </div>
            <div class="card-body">
                {{-- Amount Summary --}}
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
                    <form action="{{ route('payment.sslcommerz.pay', $booking) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="amount" class="form-label"><strong>Payment Amount <span class="text-danger">*</span></strong></label>
                            <div class="input-group">
                                <input type="number" id="amount" name="amount" class="form-control form-control-lg"
                                       placeholder="0.00" step="0.01" min="10" max="{{ $dueAmount }}" required
                                       value="{{ old('amount', $dueAmount) }}"
                                       @error('amount') is-invalid @enderror>
                                <span class="input-group-text">BDT</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Minimum: 10.00 BDT | Maximum: {{ number_format($dueAmount, 2) }} BDT
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="payment_type" class="form-label"><strong>Payment Type <span class="text-danger">*</span></strong></label>
                            <select id="payment_type" name="payment_type" class="form-select form-select-lg" required>
                                @foreach($types as $t)
                                    <option value="{{ $t }}" @if(old('payment_type') == $t) selected @endif>
                                        @if($t == 'advance')
                                            Advance Payment
                                        @else
                                            Balance Payment
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Security notice --}}
                        <div class="alert border-0 mb-4" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); border-left: 4px solid #198754 !important;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lock text-success me-3 mt-1" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong class="text-success">Secure Payment</strong>
                                    <p class="mb-0 text-muted small">
                                        You will be redirected to SSLCommerz secure payment gateway.
                                        Your payment information is encrypted and secure. We support Visa, MasterCard, bKash, Nagad, Rocket, and 30+ payment methods.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lg btn-primary w-100 py-3" style="font-size: 1.1rem; font-weight: 600; background: linear-gradient(135deg, #0D6EFF 0%, #0052CC 100%); border: none; border-radius: 10px;">
                            <i class="fas fa-lock me-2"></i> Pay Now with SSLCommerz
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('payment.create', $booking) }}" class="btn btn-link text-muted">
                            <i class="fas fa-money-bill-wave"></i> Or record a manual payment instead
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Rooms Booked --}}
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

        {{-- Payment Methods Info --}}
        <div class="card shadow-sm border-start border-primary" style="border-left: 4px solid #0D6EFF !important;">
            <div class="card-header bg-primary bg-opacity-10 border-bottom">
                <h6 class="mb-0 text-dark"><i class="fas fa-wallet text-primary"></i> Accepted Payments</h6>
            </div>
            <div class="card-body small">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fab fa-cc-visa text-primary" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">Visa</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fab fa-cc-mastercard text-danger" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">MasterCard</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fas fa-mobile-alt text-success" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">bKash</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fas fa-mobile-alt text-warning" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">Nagad</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fas fa-university text-info" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">Net Banking</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <i class="fas fa-ellipsis-h text-secondary" style="font-size: 1.5rem;"></i>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.7rem;">30+ More</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
