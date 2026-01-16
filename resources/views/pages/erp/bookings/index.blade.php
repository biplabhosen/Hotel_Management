@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Bookings</h3>
    <a href="{{ url('booking/create') }}" class="btn btn-primary">+ New Booking</a>
</div>
@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
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
                        <th>Payments</th>
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
                                <small class="text-muted">{{ $booking->guest->phone ?? '' }} <br> {{ $booking->guest->email ?? '' }}</small>
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
                            <td class="text-center">
                                @php
                                    $ps = 'unpaid';
                                    if((float)($booking->due_amount ?? 0) <= 0){
                                        $ps = 'paid';
                                    } elseif((float)($booking->paid_amount ?? 0) > 0 && (float)($booking->due_amount ?? 0) > 0){
                                        $ps = 'partial';
                                    }
                                @endphp

                                <a href="{{ route('booking.show', $booking) }}" class="btn btn-sm btn-outline-info position-relative">
                                    <span class="fw-bold">Payments</span>
                                    @if($ps === 'paid')
                                        <span class="badge bg-success ms-2">Paid</span>
                                    @elseif($ps === 'partial')
                                        <span class="badge bg-warning ms-2 text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Unpaid</span>
                                    @endif

                                    @if((float)($booking->due_amount ?? 0) > 0)
                                        <br>
                                        <small class=" text-danger">Due: {{ $booking->due_amount }}</small>
                                    @endif

                                    <br>
                                    <small class="text-muted">{{ $booking->payments_count ?? 0 }} payment(s)</small>
                                </a>
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
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    <a href="{{ url('booking/'.$booking->id) }}" class="btn btn-sm btn-outline-primary">View</a>

                                    @php
                                        // determine visible check-in/out button
                                        $showCheckIn = ($booking->status === 'reserved' && $booking->arrival === now()->toDateString());
                                        $showCheckOut = ($booking->status === 'checked_in' && $booking->departure === now()->toDateString());

                                        // add payment rule per spec
                                        $canAddPayment = false;
                                        if((float)($booking->due_amount ?? 0) > 0 && !in_array($booking->status, ['cancelled','no_show'])){
                                            if($booking->status === 'checked_out'){
                                                $canAddPayment = auth()->user()->hasRole('manager');
                                            } else {
                                                $canAddPayment = true;
                                            }
                                        }
                                    @endphp

                                    @if($showCheckIn)
                                        <form method="POST" action="{{ url('booking/check-in',$booking->id) }}" onsubmit="return confirm('Confirm check-in for this booking?')">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">Check In</button>
                                        </form>
                                    @endif

                                    @if($showCheckOut)
                                        <form method="POST" action="{{ url('booking/check-out/'.$booking->id) }}" onsubmit="return confirm('Confirm check-out for this booking?')">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" type="submit">Check Out</button>
                                        </form>
                                    @endif

                                    <!-- Ellipsis dropdown for secondary actions -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">…</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($canAddPayment)
                                                <li>
                                                    <a href="#" class="dropdown-item btn-add-payment" data-booking-id="{{ $booking->id }}" data-due="{{ $booking->computed_due_amount ?? '0.00' }}" data-guest="{{ $booking->guest->full_name ?? '' }}">Record Payment</a>
                                                </li>
                                            @endif

                                            @if($booking->status === 'checked_out')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('payment.invoice', $booking) }}">Generate Invoice</a>
                                                </li>
                                            @endif

                                            <li>
                                                <form action="{{ route('booking.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel booking?')">
                                                    @csrf
                                                    <button class="dropdown-item text-danger" type="submit">Cancel Booking</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">No bookings found.</td>
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



<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm" method="POST" action="">
                @csrf
                <input type="hidden" name="booking_id" id="pm_booking_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Booking</label>
                        <div id="pm_booking_info" class="fw-bold">-</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount (BDT)</label>
                        <input id="pm_amount" name="amount" type="number" step="0.01" min="0.01" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Method</label>
                        <select name="method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="advance">Advance</option>
                            <option value="balance" selected>Balance</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference (optional)</label>
                        <input name="reference" type="text" class="form-control">
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        var modalEl = document.getElementById('paymentModal');
        if (!modalEl) return;
        var paymentModal = new bootstrap.Modal(modalEl);

        document.querySelectorAll('.btn-add-payment').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var id = this.dataset.bookingId;
                var due = this.dataset.due || '0.00';
                var guest = this.dataset.guest || '';

                var form = document.getElementById('paymentForm');
                form.action = '/payment/booking/' + id;
                document.getElementById('pm_booking_id').value = id;
                document.getElementById('pm_booking_info').textContent = 'Booking #' + id + (guest ? (' - ' + guest) : '');
                document.getElementById('pm_amount').value = parseFloat(due).toFixed(2);

                paymentModal.show();
            });
        });

        // If validation failed and old booking_id exists, reopen modal with old values
        @if($errors->any() && old('booking_id'))
            var oldId = '{{ old('booking_id') }}';
            var form = document.getElementById('paymentForm');
            form.action = '/payment/booking/' + oldId;
            document.getElementById('pm_booking_id').value = oldId;
            document.getElementById('pm_booking_info').textContent = 'Booking #' + oldId;
            document.getElementById('pm_amount').value = '{{ old('amount') ?? '' }}';
            paymentModal.show();
        @endif
    });
</script>
@endsection

@endsection

