@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Invoice - Booking #{{ $booking->id }}</h2>
        <small class="text-muted">Generated on {{ now()->format('Y-m-d') }}</small>
    </div>
    <div>
        <a href="{{ url('payment/booking/'.$booking->id) }}" class="btn btn-outline-secondary">← Back</a>
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Guest: {{ $booking->guest->full_name }}</h5>
        <p>Phone: {{ $booking->guest->phone }}</p>

        <hr>
        <p><strong>Amount:</strong> BDT {{ number_format($total,2) }}</p>
        <p><strong>Paid:</strong> BDT {{ number_format($paidAmount,2) }}</p>
        <p><strong>Due:</strong> BDT {{ number_format($dueAmount,2) }}</p>

        <hr>
        <p>Thanks for your business.</p>
    </div>
</div>
@endsection
