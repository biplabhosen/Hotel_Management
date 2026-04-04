@extends('layout.erp.app')

@section('css')
<style>
/* ─────────────────────────────────────────────
   ENTERPRISE PAYMENT PAGE — DESIGN SYSTEM
   Shared with payment/create.blade.php
───────────────────────────────────────────── */
.ep-page { --ep-primary: #4338ca; --ep-primary-light: #eef2ff; --ep-primary-border: #c7d2fe;
           --ep-success: #059669; --ep-success-light: #ecfdf5; --ep-success-border: #a7f3d0;
           --ep-warning: #d97706; --ep-warning-light: #fffbeb; --ep-warning-border: #fde68a;
           --ep-danger:  #dc2626; --ep-danger-light:  #fef2f2; --ep-danger-border:  #fecaca;
           --ep-text: #0f172a; --ep-text-muted: #64748b;
           --ep-border: #e2e8f0; --ep-border-light: #f1f5f9;
           --ep-radius: 10px; --ep-radius-sm: 6px;
           --ep-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04); }

.ep-page-header { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--ep-border); }
.ep-page-header h2 { font-size: 1.45rem; font-weight: 700; color: var(--ep-text); letter-spacing: -0.3px; margin-bottom: 4px; }
.ep-breadcrumb { font-size: 0.84rem; color: var(--ep-text-muted); }
.ep-breadcrumb strong { color: #334155; }
.ep-back-btn { font-size: 0.84rem; font-weight: 500; color: #475569; padding: 8px 16px;
               border: 1.5px solid var(--ep-border); border-radius: 8px; text-decoration: none;
               background: #fff; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; }
.ep-back-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }

.ep-card { background: #fff; border-radius: var(--ep-radius); border: 1px solid var(--ep-border);
           box-shadow: var(--ep-shadow); overflow: hidden; margin-bottom: 20px; }
.ep-card-header { padding: 14px 20px; background: #f8fafc; border-bottom: 1px solid var(--ep-border);
                  display: flex; align-items: center; gap: 8px; }
.ep-card-header-primary { background: rgba(67, 56, 202, 0.05); border-bottom: 1px solid rgba(67, 56, 202, 0.1); }
.ep-card-header-primary .ep-card-icon { color: #4338ca !important; }
.ep-card-title { font-size: 0.875rem; font-weight: 600; color: #334155; margin: 0; }
.ep-card-title-white { color: #1e293b !important; }
.ep-card-icon { font-size: 0.9rem; }
.ep-card-body { padding: 20px; }

.ep-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.9px;
            color: var(--ep-text-muted); margin-bottom: 5px; }
.ep-value { font-size: 0.92rem; font-weight: 500; color: var(--ep-text); margin-bottom: 0; }
.ep-value-lg { font-size: 1.05rem; font-weight: 600; }
.ep-info-cell { padding: 12px 14px; background: #f8fafc; border-radius: var(--ep-radius-sm);
                border: 1px solid var(--ep-border-light); }

.ep-date-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px;
                 border-radius: var(--ep-radius-sm); font-size: 0.84rem; font-weight: 600; }
.ep-date-in  { background: var(--ep-success-light); color: #065f46; border: 1px solid var(--ep-success-border); }
.ep-date-out { background: var(--ep-danger-light);  color: #991b1b; border: 1px solid var(--ep-danger-border); }

.ep-amount-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px; }
.ep-amount-card { border-radius: var(--ep-radius-sm); padding: 18px 14px; text-align: center; border: 1px solid; transition: transform .2s; }
.ep-amount-card:hover { transform: translateY(-2px); }
.ep-ac-label { font-size: 0.69rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 10px; }
.ep-ac-value { font-size: 1.55rem; font-weight: 800; line-height: 1; margin-bottom: 4px; letter-spacing: -0.5px; }
.ep-ac-cur   { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px; }

.ep-ac-total { background: rgba(67, 56, 202, 0.04); border: 1px solid rgba(67, 56, 202, 0.15); }
.ep-ac-total .ep-ac-label { color: #4338ca; } .ep-ac-total .ep-ac-value { color: #312e81; } .ep-ac-total .ep-ac-cur { color: #6366f1; }
.ep-ac-paid  { background: rgba(5, 150, 105, 0.04); border: 1px solid rgba(5, 150, 105, 0.15); }
.ep-ac-paid .ep-ac-label  { color: #059669; } .ep-ac-paid .ep-ac-value  { color: #064e3b; } .ep-ac-paid .ep-ac-cur  { color: #059669; }
.ep-ac-due   { background: rgba(217, 119, 6, 0.04); border: 1px solid rgba(217, 119, 6, 0.2); }
.ep-ac-due .ep-ac-label   { color: #d97706; } .ep-ac-due .ep-ac-value   { color: #78350f; } .ep-ac-due .ep-ac-cur   { color: #d97706; }

.ep-form-label { font-size: 0.8rem; font-weight: 700; color: #374151; letter-spacing: 0.2px; margin-bottom: 7px; }
.ep-form-label .req { color: #dc2626; margin-left: 2px; }
.ep-form-input, .ep-form-select { width: 100%; border-radius: 8px; border: 1.5px solid #cbd5e1;
    padding: 10px 14px; font-size: 0.92rem; color: var(--ep-text); background: #fff;
    transition: border-color .2s, box-shadow .2s; outline: none; appearance: none; }
.ep-form-input:focus, .ep-form-select:focus { border-color: #4338ca; box-shadow: 0 0 0 3px rgba(67,56,202,.1); }
.ep-form-input.is-invalid { border-color: #dc2626; }
.ep-input-group { display: flex; }
.ep-input-group .ep-form-input { border-radius: 8px 0 0 8px; border-right: none; flex: 1; }
.ep-input-group-text { padding: 10px 16px; background: #f1f5f9; border: 1.5px solid #cbd5e1;
    border-left: none; border-radius: 0 8px 8px 0; font-size: 0.8rem; font-weight: 700;
    color: #475569; letter-spacing: 0.3px; white-space: nowrap; }
.ep-form-hint { font-size: 0.78rem; color: var(--ep-text-muted); margin-top: 5px; }
.ep-form-error { font-size: 0.78rem; color: #dc2626; margin-top: 5px; }

.ep-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px;
          font-weight: 600; font-size: 0.88rem; padding: 11px 20px; border-radius: 8px;
          border: none; cursor: pointer; transition: all .2s; text-decoration: none; }
.ep-btn-primary { background: #4f46e5; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.ep-btn-primary:hover { background: #4338ca; opacity: 0.95; transform: translateY(-1px); color: #fff; }
.ep-btn-ghost { background: transparent; color: #475569; border: 1.5px solid #cbd5e1; }
.ep-btn-ghost:hover { background: #f8fafc; border-color: #94a3b8; color: #1e293b; }
.ep-btn-link { background: transparent; color: var(--ep-text-muted); font-size: 0.82rem; font-weight: 500; padding: 8px 12px; border-radius: 6px; }
.ep-btn-link:hover { background: #f8fafc; color: #4338ca; }
.ep-btn-xl { padding: 14px 28px; font-size: 1rem; border-radius: 10px; }
.ep-btn-full { width: 100%; }

.ep-divider { height: 1px; background: var(--ep-border); margin: 20px 0; }
.ep-alert { border-radius: var(--ep-radius-sm); padding: 14px 18px; display: flex; align-items: flex-start; gap: 12px; }
.ep-alert-success { background: var(--ep-success-light); border: 1px solid var(--ep-success-border); color: #065f46; }
.ep-alert-icon { font-size: 1rem; margin-top: 1px; flex-shrink: 0; }
.ep-alert-title { font-size: 0.88rem; font-weight: 700; margin-bottom: 2px; }
.ep-alert-text  { font-size: 0.82rem; margin: 0; }

/* Secure payment notice */
.ep-secure-notice { border-radius: var(--ep-radius-sm); padding: 14px 18px;
    background: rgba(5, 150, 105, 0.04);
    border: 1px solid #86efac; display: flex; align-items: flex-start; gap: 14px; }
.ep-secure-notice .sn-icon { width: 36px; height: 36px; border-radius: 50%; background: #d1fae5; display: flex;
    align-items: center; justify-content: center; color: #059669; font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.ep-secure-notice .sn-title { font-size: 0.85rem; font-weight: 700; color: #065f46; margin-bottom: 3px; }
.ep-secure-notice .sn-text  { font-size: 0.78rem; color: #047857; margin: 0; line-height: 1.5; }

/* Booking banner */
.ep-booking-banner { border-radius: var(--ep-radius-sm); padding: 16px 18px;
    background: rgba(67, 56, 202, 0.04); border: 1px solid rgba(67, 56, 202, 0.1);
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.ep-booking-banner .bk-label { font-size: 0.68rem; font-weight: 700; letter-spacing: 1px; color: #4338ca; text-transform: uppercase; margin-bottom: 3px; }
.ep-booking-banner .bk-id    { font-size: 1.4rem; font-weight: 800; color: #1e293b; letter-spacing: -0.5px; }
.ep-booking-banner .bk-icon  { width: 36px; height: 36px; border-radius: 50%; background: rgba(67, 56, 202, 0.1); display: flex; align-items: center; justify-content: center; color: #4338ca; font-size: 1rem; }
.ep-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.ep-stat-cell { padding: 10px 12px; background: #f8fafc; border-radius: var(--ep-radius-sm); border: 1px solid var(--ep-border-light); }

/* Room cards */
.ep-room-card { padding: 12px 14px; border-radius: var(--ep-radius-sm); border: 1px solid var(--ep-border);
                background: #fff; margin-bottom: 10px; transition: background .2s; }
.ep-room-card:last-child { margin-bottom: 0; }
.ep-room-card:hover { background: #f8fafc; }
.ep-room-num  { font-weight: 700; font-size: 0.9rem; color: #1e1b4b; }
.ep-room-type { font-size: 0.68rem; font-weight: 700; padding: 2px 9px; border-radius: 20px;
                background: #eef2ff; color: #3730a3; letter-spacing: 0.3px; }
.ep-room-meta { font-size: 0.78rem; color: var(--ep-text-muted); margin-top: 6px; }
.ep-room-meta span { margin-right: 12px; }

/* Payment methods grid */
.ep-methods-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.ep-method-pill { padding: 10px 6px; border-radius: var(--ep-radius-sm); background: #f8fafc;
                  border: 1px solid var(--ep-border-light); text-align: center; transition: border-color .2s; }
.ep-method-pill:hover { border-color: var(--ep-primary-border); background: #eef2ff; }
.ep-method-pill .mp-icon { font-size: 1.3rem; margin-bottom: 5px; }
.ep-method-pill .mp-name { font-size: 0.68rem; font-weight: 600; color: var(--ep-text-muted); letter-spacing: 0.2px; }

@media (max-width: 575px) {
    .ep-amount-grid { grid-template-columns: 1fr; }
    .ep-stat-row    { grid-template-columns: 1fr; }
    .ep-methods-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endsection

@section('content')

@if ($errors->any())
<div class="ep-alert ep-alert-danger mb-4" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b;" role="alert">
    <div class="ep-alert-icon"><i class="fas fa-exclamation-circle"></i></div>
    <div>
        <p class="ep-alert-title">Please correct the following errors:</p>
        <ul class="ep-alert-text mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="ep-page">

    {{-- ── Page Header ─────────────────────────────────────── --}}
    <div class="ep-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2>
                <i class="fas fa-lock" style="color:#4338ca;"></i>
                Secure Online Payment
            </h2>
            <p class="ep-breadcrumb mb-0">
                <i class="fas fa-hashtag" style="font-size:.75rem;"></i>
                Booking <strong>#{{ $booking->id }}</strong> &nbsp;·&nbsp; {{ $booking->guest->full_name }}
            </p>
        </div>
        <a href="{{ route('booking.show', $booking) }}" class="ep-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Booking
        </a>
    </div>

    @php
        $firstRoom    = $booking->bookingRooms->first();
        $lastRoom     = $booking->bookingRooms->sortByDesc('check_out')->first();
        $checkInDate  = $firstRoom ? \Carbon\Carbon::parse($firstRoom->check_in)  : null;
        $checkOutDate = $lastRoom  ? \Carbon\Carbon::parse($lastRoom->check_out)  : null;
    @endphp

    <div class="row g-4">

        {{-- ── Left Column ──────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Guest Information --}}
            <div class="ep-card" style="border-left:4px solid #4338ca;">
                <div class="ep-card-header">
                    <i class="fas fa-user ep-card-icon" style="color:#4338ca;"></i>
                    <h6 class="ep-card-title">Guest Information</h6>
                </div>
                <div class="ep-card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="ep-info-cell">
                                <p class="ep-label">Guest Name</p>
                                <p class="ep-value ep-value-lg">{{ $booking->guest->full_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ep-info-cell">
                                <p class="ep-label">Email Address</p>
                                <p class="ep-value" style="word-break:break-all;">
                                    <i class="fas fa-envelope" style="color:#4338ca; font-size:.75rem;"></i>
                                    {{ $booking->guest->email ?? 'Not provided' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="ep-info-cell">
                                <p class="ep-label">Phone</p>
                                <p class="ep-value">
                                    <i class="fas fa-phone" style="color:#059669; font-size:.75rem;"></i>
                                    {{ $booking->guest->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="ep-label">Check-in Date</p>
                            <span class="ep-date-badge ep-date-in">
                                <i class="fas fa-calendar-check" style="font-size:.75rem;"></i>
                                {{ $checkInDate ? $checkInDate->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <p class="ep-label">Check-out Date</p>
                            <span class="ep-date-badge ep-date-out">
                                <i class="fas fa-calendar-times" style="font-size:.75rem;"></i>
                                {{ $checkOutDate ? $checkOutDate->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secure Payment Form --}}
            <div class="ep-card">
                <div class="ep-card-header ep-card-header-primary">
                    <i class="fas fa-shield-alt ep-card-icon" style="color:rgba(255,255,255,.85);"></i>
                    <h6 class="ep-card-title ep-card-title-white">Secure Online Payment</h6>
                    <span class="ep-card-badge">SSLCommerz</span>
                </div>
                <div class="ep-card-body">

                    {{-- Amount Summary --}}
                    <div class="ep-amount-grid">
                        <div class="ep-amount-card ep-ac-total">
                            <p class="ep-ac-label">Total Amount</p>
                            <p class="ep-ac-value">{{ number_format($total, 2) }}</p>
                            <p class="ep-ac-cur">BDT</p>
                        </div>
                        <div class="ep-amount-card ep-ac-paid">
                            <p class="ep-ac-label"><i class="fas fa-check-circle" style="font-size:.7rem;"></i> Already Paid</p>
                            <p class="ep-ac-value">{{ number_format($paidAmount, 2) }}</p>
                            <p class="ep-ac-cur">BDT</p>
                        </div>
                        <div class="ep-amount-card ep-ac-due">
                            <p class="ep-ac-label"><i class="fas fa-clock" style="font-size:.7rem;"></i> Balance Due</p>
                            <p class="ep-ac-value">{{ number_format($dueAmount, 2) }}</p>
                            <p class="ep-ac-cur">BDT</p>
                        </div>
                    </div>

                    @if($dueAmount <= 0)
                        <div class="ep-alert ep-alert-success">
                            <div class="ep-alert-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <p class="ep-alert-title">Booking Fully Settled</p>
                                <p class="ep-alert-text">All payments have been received. No further payment is required for this booking.</p>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('payment.sslcommerz.pay', $booking) }}" method="POST">
                            @csrf

                            {{-- Amount --}}
                            <div class="mb-4">
                                <label for="amount" class="ep-form-label">
                                    Payment Amount <span class="req">*</span>
                                </label>
                                <div class="ep-input-group">
                                    <input type="number" id="amount" name="amount"
                                           class="ep-form-input @error('amount') is-invalid @enderror"
                                           placeholder="0.00" step="0.01" min="10"
                                           max="{{ $dueAmount }}" required
                                           value="{{ old('amount', $dueAmount) }}"
                                           style="font-size:1.05rem; font-weight:700;">
                                    <span class="ep-input-group-text">BDT</span>
                                </div>
                                @error('amount')
                                    <p class="ep-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                                <p class="ep-form-hint">
                                    <i class="fas fa-info-circle" style="color:#4338ca;"></i>
                                    Min: <strong>10.00 BDT</strong> &nbsp;·&nbsp; Max: <strong>{{ number_format($dueAmount, 2) }} BDT</strong>
                                </p>
                            </div>

                            {{-- Payment Type --}}
                            <div class="mb-4">
                                <label for="payment_type" class="ep-form-label">
                                    Payment Type <span class="req">*</span>
                                </label>
                                <select id="payment_type" name="payment_type" class="ep-form-select" required>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" @selected(old('payment_type') == $t)>
                                            {{ $t === 'advance' ? 'Advance Payment' : 'Balance Payment' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Secure Notice --}}
                            <div class="ep-secure-notice mb-5">
                                <div class="sn-icon"><i class="fas fa-lock"></i></div>
                                <div>
                                    <p class="sn-title">256-bit SSL Encrypted Transaction</p>
                                    <p class="sn-text">
                                        You will be redirected to the SSLCommerz secure payment gateway.
                                        We accept Visa, MasterCard, bKash, Nagad, Rocket, and 30+ payment methods.
                                        Your payment data is fully encrypted and never stored on our servers.
                                    </p>
                                </div>
                            </div>

                            <button type="submit" class="ep-btn ep-btn-primary ep-btn-xl ep-btn-full mb-3">
                                <i class="fas fa-lock"></i>
                                Proceed to SSLCommerz Payment
                            </button>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('payment.create', $booking) }}" class="ep-btn ep-btn-link">
                                <i class="fas fa-money-bill-wave"></i>
                                Record a manual payment instead
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Right Sidebar ────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Booking Summary --}}
            <div class="ep-card" style="border-top:3px solid #4338ca;">
                <div class="ep-card-header">
                    <i class="fas fa-receipt ep-card-icon" style="color:#4338ca;"></i>
                    <h6 class="ep-card-title">Booking Summary</h6>
                </div>
                <div class="ep-card-body">
                    <div class="ep-booking-banner">
                        <div>
                            <p class="bk-label">Booking ID</p>
                            <p class="bk-id">#{{ $booking->id }}</p>
                        </div>
                        <div class="bk-icon"><i class="fas fa-ticket-alt"></i></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <p class="ep-label">Check-in</p>
                            <span class="ep-date-badge ep-date-in" style="font-size:.78rem; padding:4px 10px;">
                                <i class="fas fa-calendar-check" style="font-size:.7rem;"></i>
                                {{ $checkInDate ? $checkInDate->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <p class="ep-label">Check-out</p>
                            <span class="ep-date-badge ep-date-out" style="font-size:.78rem; padding:4px 10px;">
                                <i class="fas fa-calendar-times" style="font-size:.7rem;"></i>
                                {{ $checkOutDate ? $checkOutDate->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="ep-divider" style="margin:14px 0;"></div>

                    <div class="ep-stat-row">
                        <div class="ep-stat-cell">
                            <p class="ep-label">Rooms</p>
                            <p class="ep-value"><strong>{{ $booking->bookingRooms->count() }}</strong></p>
                        </div>
                        <div class="ep-stat-cell">
                            <p class="ep-label">Guests</p>
                            <p class="ep-value"><strong>{{ $booking->total_guests }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rooms Booked --}}
            <div class="ep-card" style="border-left:4px solid #059669;">
                <div class="ep-card-header">
                    <i class="fas fa-door-open ep-card-icon" style="color:#059669;"></i>
                    <h6 class="ep-card-title">Rooms Booked</h6>
                </div>
                <div class="ep-card-body">
                    @forelse($booking->bookingRooms as $br)
                        <div class="ep-room-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="ep-room-num">Room {{ $br->room->room_number }}</span>
                                <span class="ep-room-type">{{ $br->room->roomType->name ?? 'Standard' }}</span>
                            </div>
                            <div class="ep-room-meta">
                                <span>
                                    <i class="fas fa-calendar-alt" style="color:#94a3b8; font-size:.72rem;"></i>
                                    {{ \Carbon\Carbon::parse($br->check_in)->format('d M') }} &mdash; {{ \Carbon\Carbon::parse($br->check_out)->format('d M Y') }}
                                </span>
                                <span>
                                    <i class="fas fa-moon" style="color:#d97706; font-size:.72rem;"></i>
                                    {{ max(1, \Carbon\Carbon::parse($br->check_in)->diffInDays($br->check_out)) }} nights &times;
                                    <strong style="color:#059669;">{{ number_format($br->price_per_night, 2) }}</strong> BDT
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="ep-form-hint mb-0">No rooms assigned to this booking.</p>
                    @endforelse
                </div>
            </div>

            {{-- Accepted Payment Methods --}}
            <div class="ep-card" style="border-left:4px solid #4338ca;">
                <div class="ep-card-header">
                    <i class="fas fa-wallet ep-card-icon" style="color:#4338ca;"></i>
                    <h6 class="ep-card-title">Accepted Methods</h6>
                </div>
                <div class="ep-card-body">
                    <div class="ep-methods-grid">
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fab fa-cc-visa" style="color:#1a1f71;"></i></div>
                            <p class="mp-name">Visa</p>
                        </div>
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fab fa-cc-mastercard" style="color:#eb001b;"></i></div>
                            <p class="mp-name">Mastercard</p>
                        </div>
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fas fa-mobile-alt" style="color:#e2136e;"></i></div>
                            <p class="mp-name">bKash</p>
                        </div>
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fas fa-mobile-alt" style="color:#f05a28;"></i></div>
                            <p class="mp-name">Nagad</p>
                        </div>
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fas fa-university" style="color:#4338ca;"></i></div>
                            <p class="mp-name">Net Banking</p>
                        </div>
                        <div class="ep-method-pill">
                            <div class="mp-icon"><i class="fas fa-ellipsis-h" style="color:#64748b;"></i></div>
                            <p class="mp-name">30+ More</p>
                        </div>
                    </div>
                    <p class="ep-form-hint text-center mt-3 mb-0">
                        <i class="fas fa-lock" style="color:#4338ca;"></i>
                        Powered by <strong>SSLCommerz</strong> — Trusted Payment Gateway
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
