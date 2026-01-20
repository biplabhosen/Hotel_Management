<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 640px;
            margin: auto;
            border: 1px solid #e0e0e0;
            padding: 24px;
        }

        .header {
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .hotel-name {
            font-size: 20px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 18px;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
    </style>
</head>

<body>
    <div class="container">

        ```
        <div class="header">
            <div class="hotel-name">{{ $hotel->name }}</div>
            <div>{{ $hotel->address }}</div>
            <div>Phone: {{ $hotel->phone }} | Email: {{ $hotel->email }}</div>
        </div>

        <div class="section">
            Dear {{ $booking->guest->full_name }},
        </div>

        <div class="section">
            <strong>Congratulations!</strong>
            Your reservation at <strong>{{ $hotel->name }}</strong> has been successfully confirmed.
        </div>

        <div class="section">
            Below are your booking details for your reference:
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Booking Reference</th>
                    <td>#{{ $booking->id }}</td>
                </tr>
                <tr>
                    <th>Check-in</th>
                    <td>{{ \Carbon\Carbon::parse($booking->check_in)->toFormattedDateString() }}</td>
                </tr>
                <tr>
                    <th>Check-out</th>
                    <td>{{ \Carbon\Carbon::parse($booking->check_out)->toFormattedDateString() }}</td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>BDT {{ number_format($booking->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>{{ ucfirst($booking->payment_status) }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <span class="label">Reserved Rooms:</span>
            <table>
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Nights</th>
                        <th>Rate (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($booking->bookingRooms as $br)
                        <tr>
                            <td>
                                Room {{ $br->room->room_number }}
                                @if ($br->room->roomType)
                                    – {{ $br->room->roomType->name }}
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($br->check_in)->diffInDays($br->check_out) }}
                            </td>
                            <td>
                                {{ number_format($br->price_per_night, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            If you need to modify or cancel your reservation, or if you have any special requests,
            please contact us directly using the details above.
        </div>

        <div class="section">
            We look forward to welcoming you and wish you a pleasant stay at
            <strong>{{ $hotel->name }}</strong>.
        </div>

        <div class="footer">
            This is an automated confirmation email from {{ $hotel->name }} PMS.<br>
            Please do not reply directly to this message.
        </div>
        ```

    </div>
</body>

</html>
