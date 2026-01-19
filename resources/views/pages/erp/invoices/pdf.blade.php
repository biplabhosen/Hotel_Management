<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #222;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .hotel-name {
            font-size: 18px;
            font-weight: bold;
        }

        .invoice-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .meta-table, .info-table {
            width: 100%;
            margin-bottom: 12px;
        }

        .meta-table td {
            vertical-align: top;
        }

        .box {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table.charges {
            width: 100%;
            border-collapse: collapse;
        }

        table.charges th, table.charges td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        table.charges th {
            background-color: #f5f5f5;
            text-align: left;
        }

        .summary {
            width: 40%;
            float: right;
            margin-top: 10px;
        }

        .summary td {
            padding: 5px;
        }

        .summary .label {
            text-align: right;
        }

        .summary .amount {
            text-align: right;
            font-weight: bold;
        }

        .status {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .paid { background: #d4edda; color: #155724; }
        .partial { background: #fff3cd; color: #856404; }
        .unpaid { background: #f8d7da; color: #721c24; }

        .footer {
            border-top: 1px solid #ddd;
            margin-top: 40px;
            padding-top: 10px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>

<div class="header">
    <table width="100%">
        <tr>
            <td>
                <div class="hotel-name">{{ $hotel->name }}</div>
                <div>{{ $hotel->address }}</div>
                <div>Phone: {{ $hotel->phone }}</div>
                <div>Email: {{ $hotel->email }}</div>
            </td>
            <td class="invoice-title">
                INVOICE<br>
                <small>#{{ $booking->invoice_number ?? 'INV-'.now()->year .now()->month .'000' . $booking->id }}</small>
            </td>
        </tr>
    </table>
</div>

<table class="meta-table">
    <tr>
        <td class="box" width="50%">
            <strong>Billed To</strong><br>
            {{ $booking->guest->full_name }}<br>
            {{ $booking->guest->phone }}<br>
            {{ $booking->guest->email }}
        </td>
        <td class="box" width="50%">
            <strong>Invoice Details</strong><br>
            Invoice Date: {{ now()->toDateString() }}<br>
            Booking Ref: {{ $booking->id }}<br>
            Currency: BDT
        </td>
    </tr>
</table>

<table class="charges">
    <thead>
        <tr>
            <th>Description</th>
            <th>Nights</th>
            <th>Rate</th>
            <th align="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($booking->bookingRooms as $br)
            @php
                $nights = max(1,
                    \Carbon\Carbon::parse($br->check_in)
                        ->diffInDays($br->check_out)
                );
                $amount = $nights * $br->price_per_night;
            @endphp
            <tr>
                <td>
                    Room {{ $br->room->room_number }}-{{ $br->room->roomType->name }}
                </td>
                <td>{{ $nights }}</td>
                <td>{{ number_format($br->price_per_night, 2) }}</td>
                <td align="right">{{ number_format($amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="summary">
    <tr>
        <td class="label">Subtotal</td>
        <td class="amount">{{ number_format($total, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Paid</td>
        <td class="amount">{{ number_format($paid, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Due</td>
        <td class="amount">{{ number_format($due, 2) }}</td>
    </tr>
    <tr>
        <td class="label">Status</td>
        <td class="amount">
            <span class="status {{ $booking->payment_status }}">
                {{ strtoupper($booking->payment_status) }}
            </span>
        </td>
    </tr>
</table>

<div style="clear: both;"></div>

<div class="footer">
    This is a system-generated invoice from {{ $hotel->name }} PMS.<br>
    Generated on {{ now() }} | No signature required.
</div>

</body>
</html>
