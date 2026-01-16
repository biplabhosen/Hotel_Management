@extends('layout.erp.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Payment Receipt - #{{ $payment->id }}</h2>
        <small class="text-muted">{{ $payment->payment_date->format('Y-m-d') }} | {{ $payment->method }}</small>
    </div>
    <div>
        <a href="{{ url('payment/booking/'.$payment->booking_id) }}" class="btn btn-outline-secondary">← Back</a>
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Guest: {{ $payment->booking->guest->full_name }}</h5>
        <p>Booking #{{ $payment->booking->id }}</p>
        <hr>
        <p><strong>Amount:</strong> BDT {{ number_format($payment->amount,2) }}</p>
        <p><strong>Method:</strong> {{ ucfirst(str_replace('_',' ', $payment->method)) }}</p>
        <p><strong>Reference:</strong> {{ $payment->reference ?? '-' }}</p>
        <hr>
        <p>Thank you.</p>
    </div>
</div>
@endsection
