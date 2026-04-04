@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0" style="font-weight: 600;">
            <i class="fas fa-file-invoice-dollar text-primary"></i> Payment Details
        </h2>
        <small class="text-muted">Booking #{{ $booking->id }} - Manage payments and track payment status</small>
    </div>
    <a href="{{ url('payment') }}" class="btn btn-outline-secondary">← Back to Payments</a>
</div>

@php
    $checkIn = $booking->bookingRooms->min('check_in');
    $checkOut = $booking->bookingRooms->max('check_out');
    $arrival = $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d M Y') : 'N/A';
    $departure = $checkOut ? \Carbon\Carbon::parse($checkOut)->format('d M Y') : 'N/A';
@endphp
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Guest Info Card - Prominent at Top -->
        <div class="card shadow-sm mb-4 border-0" style="border: 1px solid rgba(67, 56, 202, 0.1) !important; border-top: 4px solid #4f46e5 !important;">
            <div class="card-header border-bottom" style="background: rgba(67, 56, 202, 0.02); padding: 16px 20px;">
                <h6 class="mb-0" style="color: #312e81; font-weight: 600;"><i class="fas fa-user-circle" style="color: #4f46e5; margin-right: 6px;"></i> Guest Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="mb-2"><strong class="text-dark">Guest Name</strong></p>
                            <p class="text-dark mb-0" style="font-size: 1.1rem;">{{ $booking->guest->full_name }}</p>
                        </div>

                        <div>
                            <p class="mb-2"><strong class="text-dark">Phone</strong></p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone text-primary"></i> {{ $booking->guest->phone }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="mb-2"><strong class="text-dark">Email</strong></p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope text-primary"></i> {{ $booking->guest->email ?? 'Not provided' }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-2"><strong class="text-dark">Booking Status</strong></p>
                            <p class="mb-0">
                                <span class="badge px-3 py-2 bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'checked_out' ? 'secondary' : 'info') }}">
                                    <i class="fas fa-{{ $booking->status == 'checked_in' ? 'sign-in-alt' : 'sign-out-alt' }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stay Dates Card -->
        <div class="card shadow-sm mb-4 border-0" style="border: 1px solid #e2e8f0 !important;">
            <div class="card-header border-bottom" style="background: #f8fafc; padding: 16px 20px;">
                <h6 class="mb-0" style="color: #334155; font-weight: 600;">Stay Duration</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <p class="mb-1 text-muted small" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Arrival Date</p>
                        <p class="h5 mb-0" style="color: #4338ca; font-weight: 700;">{{ $arrival }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small" style="text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Departure Date</p>
                        <p class="h5 mb-0" style="color: #059669; font-weight: 700;">{{ $departure }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary Card -->
        <div class="card shadow-sm mb-4 border-0" style="border: 1px solid rgba(5, 150, 105, 0.1) !important; border-top: 4px solid #10b981 !important;">
            <div class="card-header border-bottom" style="background: rgba(5, 150, 105, 0.02); padding: 16px 20px;">
                <h6 class="mb-0" style="color: #064e3b; font-weight: 600;"><i class="fas fa-wallet" style="color: #10b981; margin-right: 6px;"></i> Payment Summary</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-4 rounded text-center border shadow-sm" style="background: rgba(67, 56, 202, 0.03); border-color: rgba(67, 56, 202, 0.15) !important;">
                            <p class="mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #4f46e5;">Total Amount</p>
                            <h3 class="mb-0" style="font-weight: 800; font-size: 1.8rem; color: #312e81; letter-spacing: -0.5px;">{{ number_format($total, 2) }}</h3>
                            <small style="font-size: 0.8rem; font-weight: 700; color: #6366f1;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded text-center border shadow-sm" style="background: rgba(5, 150, 105, 0.03); border-color: rgba(5, 150, 105, 0.15) !important;">
                            <p class="mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #059669;">Paid Amount</p>
                            <h3 class="mb-0" style="font-weight: 800; font-size: 1.8rem; color: #064e3b; letter-spacing: -0.5px;">
                                <i class="fas fa-check-circle" style="font-size: 1.4rem;"></i> {{ number_format($paidAmount, 2) }}
                            </h3>
                            <small style="font-size: 0.8rem; font-weight: 700; color: #059669;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($dueAmount > 0)
                            <div class="p-4 rounded text-center border shadow-sm" style="background: rgba(217, 119, 6, 0.03); border-color: rgba(217, 119, 6, 0.2) !important;">
                                <p class="mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #d97706;">Due Amount</p>
                                <h3 class="mb-0" style="font-weight: 800; font-size: 1.8rem; color: #78350f; letter-spacing: -0.5px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 1.4rem;"></i> {{ number_format($dueAmount, 2) }}
                                </h3>
                                <small style="font-size: 0.8rem; font-weight: 700; color: #d97706;">BDT</small>
                            </div>
                        @else
                            <div class="p-4 rounded text-center border shadow-sm" style="background: rgba(5, 150, 105, 0.03); border-color: rgba(5, 150, 105, 0.15) !important;">
                                <p class="mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #059669;">Balance</p>
                                <h3 class="mb-0" style="font-weight: 800; font-size: 1.8rem; color: #064e3b; letter-spacing: -0.5px;">
                                    <i class="fas fa-check-double" style="font-size: 1.4rem;"></i> 0.00
                                </h3>
                                <small style="font-size: 0.8rem; font-weight: 700; color: #059669;">BDT</small>
                            </div>
                        @endif
                    </div>
                </div>

                @if($dueAmount > 0)
                    <div class="alert mt-3 mb-0" style="background: rgba(217, 119, 6, 0.04); border: 1px solid rgba(217, 119, 6, 0.2); border-left: 4px solid #f59e0b; border-radius: 6px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle" style="color: #d97706;"></i>
                                <strong style="color: #92400e; margin-left: 5px;">Outstanding Balance</strong><br>
                                <small style="color: #78350f; margin-left: 23px;">{{ number_format($dueAmount, 2) }} BDT is still pending</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('payment.sslcommerz.checkout', $booking) }}" class="btn btn-sm" style="background: #4f46e5; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;">
                                    <i class="fas fa-credit-card"></i> Pay Online
                                </a>
                                <a href="{{ route('payment.create', $booking) }}" class="btn btn-sm" style="background: #10b981; color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;">
                                    <i class="fas fa-plus-circle"></i> Record Payment
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert mt-3 mb-0" style="background: rgba(5, 150, 105, 0.04); border: 1px solid rgba(5, 150, 105, 0.2); border-left: 4px solid #10b981; border-radius: 6px;">
                        <i class="fas fa-check-circle" style="color: #059669;"></i>
                        <strong style="color: #064e3b; margin-left: 5px;">Fully Paid</strong> <span style="color: #065f46;">- No outstanding balance</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 20px; border: 1px solid #e2e8f0 !important;">
            <div class="card-header border-bottom" style="background: #f8fafc; padding: 16px 20px;">
                <h6 class="mb-0" style="color: #334155; font-weight: 600;">Booking Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted small mb-2">Rooms Booked</p>
                    <p class="h5 mb-0 text-primary">{{ $booking->bookingRooms->count() }} Room(s)</p>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted small mb-2">Total Guests</p>
                    <p class="h5 mb-0">{{ $booking->total_guests }} Guest(s)</p>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted small mb-2">Payment Records</p>
                    <p class="h5 mb-0 text-info">{{ $payments->count() }} Record(s)</p>
                </div>
                <hr>
                <div>
                    <p class="text-muted small mb-2">Payment Progress</p>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $total > 0 ? round(($paidAmount / $total) * 100, 1) : 0 }}%"></div>
                    </div>
                    <p class="text-muted small mt-1 mb-0">{{ $total > 0 ? round(($paidAmount / $total) * 100, 1) : 0 }}% Complete</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="card shadow-sm mt-4 border-0" style="border: 1px solid #e2e8f0 !important;">
    <div class="card-header border-bottom" style="background: #f8fafc; padding: 16px 20px;">
        <h6 class="mb-0" style="color: #334155; font-weight: 600;">Payment History</h6>
    </div>
    <div class="card-body p-0">
        @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 15%;">Amount</th>
                            <th style="width: 15%;">Method</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 20%;">Reference</th>
                            <th style="width: 12%;">Status</th>
                            <th style="width: 8%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td><small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small></td>
                                <td><span class="badge bg-light text-dark">{{ number_format($payment->amount, 2) }} BDT</span></td>
                                <td><small>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</small></td>
                                <td><small class="badge bg-secondary bg-opacity-25">{{ ucfirst($payment->type) }}</small></td>
                                <td><small class="text-muted">{{ $payment->reference ?? '-' }}</small></td>
                                <td>
                                    @php
                                        $pStatusColor = 'warning'; $pStatusBg = 'rgba(245, 158, 11, 0.1)'; $pStatusText = '#b45309';
                                        if($payment->status == 'paid') { $pStatusBg = 'rgba(16, 185, 129, 0.1)'; $pStatusText = '#047857'; }
                                        elseif($payment->status == 'failed') { $pStatusBg = 'rgba(239, 68, 68, 0.1)'; $pStatusText = '#b91c1c'; }
                                        elseif($payment->status == 'refunded') { $pStatusBg = 'rgba(100, 116, 139, 0.1)'; $pStatusText = '#334155'; }
                                    @endphp
                                    <span class="badge" style="background: {{ $pStatusBg }}; color: {{ $pStatusText }}; padding: 6px 10px; font-weight: 600; letter-spacing: 0.3px;">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($payment->status == 'pending')
                                        <a href="{{ route('payment.edit', $payment) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('payment.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @elseif($payment->status == 'paid')
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#refundModal{{ $payment->id }}" title="Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>

                            @if($payment->status == 'paid')
                                <!-- Refund Modal -->
                                <div class="modal fade" id="refundModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title">Process Refund</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('payment.refund', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label"><strong>Refund Amount</strong></label>
                                                        <div class="input-group">
                                                            <input type="number" name="refund_amount" class="form-control" step="0.01" max="{{ $payment->amount }}" placeholder="0.00" required>
                                                            <span class="input-group-text">BDT</span>
                                                        </div>
                                                        <small class="text-muted">Max: {{ number_format($payment->amount, 2) }} BDT</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label"><strong>Reason</strong></label>
                                                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter refund reason..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Process Refund</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-5 text-center">
                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2">No payments recorded yet.</p>
                @if($dueAmount > 0)
                    <a href="{{ route('payment.create', $booking) }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Record First Payment
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
