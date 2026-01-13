@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0" style="font-weight: 600;">Payment Management</h2>
        <small class="text-muted">Track and manage all hotel payments</small>
    </div>
    <a href="{{ url('booking') }}" class="btn btn-outline-secondary">← Back to Bookings</a>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Paid</p>
                        <h4 class="mb-0 text-success" style="font-weight: 600;">{{ number_format($summary['total_paid'], 2) }}</h4>
                        <small class="text-muted">BDT</small>
                    </div>
                    <i class="fas fa-check-circle text-success opacity-50" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Pending</p>
                        <h4 class="mb-0 text-warning" style="font-weight: 600;">{{ number_format($summary['total_pending'], 2) }}</h4>
                        <small class="text-muted">BDT</small>
                    </div>
                    <i class="fas fa-clock text-warning opacity-50" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Failed</p>
                        <h4 class="mb-0 text-danger" style="font-weight: 600;">{{ number_format($summary['total_failed'], 2) }}</h4>
                        <small class="text-muted">BDT</small>
                    </div>
                    <i class="fas fa-times-circle text-danger opacity-50" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Refunded</p>
                        <h4 class="mb-0 text-info" style="font-weight: 600;">{{ number_format($summary['total_refunded'], 2) }}</h4>
                        <small class="text-muted">BDT</small>
                    </div>
                    <i class="fas fa-undo text-info opacity-50" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @if(request('status')==$s) selected @endif>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}" @if(request('type')==$t) selected @endif>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select">
                    <option value="">All Methods</option>
                    @foreach ($methods as $m)
                        <option value="{{ $m }}" @if(request('method')==$m) selected @endif>{{ ucfirst(str_replace('_', ' ', $m)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card shadow-sm">
    <div class="card-header bg-light border-bottom">
        <h6 class="mb-0 text-dark">All Payments</h6>
    </div>
    <div class="card-body p-0">
        @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 15%;">Booking</th>
                            <th style="width: 12%;">Guest</th>
                            <th style="width: 12%;">Amount</th>
                            <th style="width: 11%;">Method</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('booking.show', $payment->booking) }}'">
                                <td>
                                    <small class="text-muted">{{ $payment->payment_date->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <strong class="text-primary">#{{ $payment->booking->id }}</strong>
                                </td>
                                <td>
                                    <small>{{ $payment->booking->guest->full_name }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark fw-bold">{{ number_format($payment->amount, 2) }} BDT</span>
                                </td>
                                <td>
                                    <small>{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</small>
                                </td>
                                <td>
                                    <small class="badge bg-secondary bg-opacity-25">{{ ucfirst($payment->type) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'failed' ? 'danger' : ($payment->status == 'refunded' ? 'secondary' : 'warning')) }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-end" onclick="event.stopPropagation();">
                                    @if($payment->status == 'pending')
                                        <a href="{{ route('payment.edit', $payment) }}" class="btn btn-sm btn-warning" title="Edit Payment">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @elseif($payment->status == 'paid')
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#refundModal{{ $payment->id }}" title="Process Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="card-footer bg-light">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <i class="fas fa-inbox text-muted" style="font-size: 2.5rem;"></i>
                <p class="text-muted mt-3">No payments found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Refund Modals -->
@foreach($payments->where('status', 'paid') as $payment)
    <div class="modal fade" id="refundModal{{ $payment->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('payment.refund', $payment) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Payment Details</strong></label>
                            <p class="text-muted small mb-0">Booking #{{ $payment->booking->id }} - {{ $payment->booking->guest->full_name }}</p>
                            <p class="text-muted small">Original Amount: <strong>{{ number_format($payment->amount, 2) }} BDT</strong></p>
                        </div>

                        <div class="mb-3">
                            <label for="refund_amount" class="form-label"><strong>Refund Amount <span class="text-danger">*</span></strong></label>
                            <div class="input-group">
                                <input type="number" id="refund_amount" name="refund_amount" class="form-control" step="0.01" max="{{ $payment->amount }}" placeholder="0.00" required>
                                <span class="input-group-text">BDT</span>
                            </div>
                            <small class="text-muted d-block mt-1">Maximum: {{ number_format($payment->amount, 2) }} BDT</small>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Refund</label>
                            <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Enter refund reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-undo"></i> Process Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
        </div>
    </div>
</div>

@if($payments->hasPages())
    <nav class="mt-4">
        {{ $payments->links() }}
    </nav>
@endif

<style>
.stat-box {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    border-left: 4px solid #0d6efd;
}
</style>
@endsection
