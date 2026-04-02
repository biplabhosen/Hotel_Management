<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment {{ ucfirst($status) }} — {{ config('app.name', 'Hotel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .result-card {
            max-width: 540px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .result-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            animation: bounceIn 0.6s ease-out 0.3s both;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            60% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .icon-success { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #198754; }
        .icon-failed { background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); color: #dc3545; }
        .icon-canceled { background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); color: #fd7e14; }
        .icon-error { background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); color: #dc3545; }

        .header-success { background: linear-gradient(135deg, #198754 0%, #155724 100%); }
        .header-failed { background: linear-gradient(135deg, #dc3545 0%, #b21f2d 100%); }
        .header-canceled { background: linear-gradient(135deg, #fd7e14 0%, #e06c0a 100%); }
        .header-error { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6c757d; font-size: 0.9rem; }
        .detail-value { font-weight: 600; color: #212529; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="result-card bg-white">
        {{-- Header --}}
        <div class="header-{{ $status }} text-white text-center py-3 px-4">
            <h5 class="mb-0">
                @if($status === 'success')
                    <i class="fas fa-shield-alt me-2"></i> Payment Complete
                @elseif($status === 'failed')
                    <i class="fas fa-times-circle me-2"></i> Payment Failed
                @elseif($status === 'canceled')
                    <i class="fas fa-ban me-2"></i> Payment Canceled
                @else
                    <i class="fas fa-exclamation-triangle me-2"></i> Error
                @endif
            </h5>
        </div>

        {{-- Body --}}
        <div class="text-center p-4 pb-2">
            <div class="result-icon icon-{{ $status }}">
                @if($status === 'success')
                    <i class="fas fa-check"></i>
                @elseif($status === 'failed')
                    <i class="fas fa-times"></i>
                @elseif($status === 'canceled')
                    <i class="fas fa-ban"></i>
                @else
                    <i class="fas fa-exclamation"></i>
                @endif
            </div>

            <h4 class="mb-2" style="font-weight: 700;">{{ $title }}</h4>
            <p class="text-muted mb-0">{{ $message }}</p>
        </div>

        {{-- Transaction Details --}}
        <div class="px-4 pb-2">
            <div class="bg-light rounded p-3">
                @if(isset($amount))
                    <div class="detail-row">
                        <span class="detail-label">Amount</span>
                        <span class="detail-value text-success">BDT {{ number_format($amount, 2) }}</span>
                    </div>
                @endif

                @if(isset($transaction_id))
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID</span>
                        <span class="detail-value" style="font-size: 0.8rem; font-family: monospace;">{{ $transaction_id }}</span>
                    </div>
                @endif

                @if(isset($booking_id) && $booking_id)
                    <div class="detail-row">
                        <span class="detail-label">Booking</span>
                        <span class="detail-value">#{{ $booking_id }}</span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value">{{ now()->format('d M Y, h:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        @if($status === 'success')
                            <span class="badge bg-success px-3">Paid</span>
                        @elseif($status === 'failed')
                            <span class="badge bg-danger px-3">Failed</span>
                        @elseif($status === 'canceled')
                            <span class="badge bg-warning text-dark px-3">Canceled</span>
                        @else
                            <span class="badge bg-secondary px-3">Error</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="p-4 text-center">
            @if(isset($booking_id) && $booking_id)
                <a href="{{ url('payment/booking/' . $booking_id) }}" class="btn btn-primary px-4 me-2" style="border-radius: 8px;">
                    <i class="fas fa-receipt me-1"></i> View Payments
                </a>
            @endif

            @if($status === 'failed' || $status === 'canceled')
                @if(isset($booking_id) && $booking_id)
                    <a href="{{ url('payment/booking/' . $booking_id . '/online') }}" class="btn btn-outline-primary px-4" style="border-radius: 8px;">
                        <i class="fas fa-redo me-1"></i> Try Again
                    </a>
                @endif
            @endif

            <a href="{{ url('/') }}" class="btn btn-outline-secondary px-4 ms-2" style="border-radius: 8px;">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
        </div>

        {{-- Footer --}}
        <div class="text-center pb-3">
            <small class="text-muted">
                <i class="fas fa-lock me-1"></i> Secured by SSLCommerz
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
