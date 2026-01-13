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

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Guest Info Card - Prominent at Top -->
        <div class="card shadow-sm mb-4 border-start border-primary" style="border-left: 4px solid #0D6EFF !important;">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 text-dark"><i class="fas fa-user-circle text-primary"></i> Guest Information</h5>
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
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark">Stay Duration</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <p class="mb-1 text-muted small">Arrival Date</p>
                        <p class="h5 mb-0 text-primary">{{ \Carbon\Carbon::parse($booking->arrival)->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Departure Date</p>
                        <p class="h5 mb-0 text-danger">{{ \Carbon\Carbon::parse($booking->departure)->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary Card -->
        <div class="card shadow-sm mb-4 border-top border-success" style="border-top: 3px solid #198754 !important;">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 text-dark"><i class="fas fa-wallet text-success"></i> Payment Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded text-center border-0 shadow-sm">
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">Total Amount</p>
                            <h3 class="mb-0 text-primary" style="font-weight: 700; font-size: 1.8rem;">{{ number_format($total, 2) }}</h3>
                            <small class="text-muted" style="font-size: 0.85rem;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #155724 100%);">
                            <p class="text-white mb-2" style="font-size: 0.9rem; opacity: 0.9;">Paid Amount</p>
                            <h3 class="mb-0 text-white" style="font-weight: 700; font-size: 1.8rem;">
                                <i class="fas fa-check-circle"></i> {{ number_format($paidAmount, 2) }}
                            </h3>
                            <small class="text-white" style="font-size: 0.85rem; opacity: 0.9;">BDT</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($dueAmount > 0)
                            <div class="p-4 rounded text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);">
                                <p class="text-dark mb-2" style="font-size: 0.9rem; font-weight: 500;">Due Amount</p>
                                <h3 class="mb-0 text-dark" style="font-weight: 700; font-size: 1.8rem;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ number_format($dueAmount, 2) }}
                                </h3>
                                <small class="text-dark" style="font-size: 0.85rem; font-weight: 500;">BDT</small>
                            </div>
                        @else
                            <div class="p-4 rounded text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #155724 100%);">
                                <p class="text-white mb-2" style="font-size: 0.9rem; opacity: 0.9;">Balance Paid</p>
                                <h3 class="mb-0 text-white" style="font-weight: 700; font-size: 1.8rem;">
                                    <i class="fas fa-check-circle"></i> {{ number_format($dueAmount, 2) }}
                                </h3>
                                <small class="text-white" style="font-size: 0.85rem; opacity: 0.9;">BDT</small>
                            </div>
                        @endif
                    </div>
                </div>

                @if($dueAmount > 0)
                    <div class="alert alert-warning mt-3 mb-0 border-warning" style="background-color: #FFF3CD; border-left: 4px solid #FFC107;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <strong>Outstanding Balance</strong><br>
                                <small class="text-dark">{{ number_format($dueAmount, 2) }} BDT is still pending</small>
                            </div>
                            <a href="{{ route('payment.create', $booking) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-plus-circle"></i> Record Payment
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success mt-3 mb-0 border-success" style="background-color: #D1E7DD; border-left: 4px solid #198754;">
                        <i class="fas fa-check-circle text-success"></i>
                        <strong>Fully Paid</strong> - No outstanding balance
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-light border-bottom">
                <h6 class="mb-0 text-dark">Booking Summary</h6>
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
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light border-bottom">
        <h6 class="mb-0 text-dark">Payment History</h6>
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
                                    <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
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
