<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SslCommerzTransaction;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\User;

class PaymentController extends Controller
{
    /**
     * Display payment history for a booking
     */
    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $payments = $booking->payments()->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $dueAmount = max(0, $total - $paidAmount);

        return view('pages.erp.payments.show', compact('booking', 'payments', 'total', 'paidAmount', 'dueAmount'));
    }

    /**
     * Display payment form for a booking
     */
    public function create(Booking $booking)
    {
        $this->authorizeBooking($booking);

        // Calculate totals
        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $payments = $booking->payments()->where('status', 'paid')->get();
        $paidAmount = $payments->sum('amount');
        $dueAmount = max(0, $total - $paidAmount);

        $methods = ['cash', 'card', 'mobile_banking', 'bank_transfer'];
        $types = ['advance', 'balance', 'refund'];

        return view('pages.erp.payments.create', compact('booking', 'total', 'paidAmount', 'dueAmount', 'methods', 'types'));
    }

    /**
     * Store a payment
     */
    public function store(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,mobile_banking,bank_transfer',
            'type' => 'required|in:advance,balance,refund',
            'reference' => 'nullable|string|max:255',
        ]);

        // Calculate due amount
        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $payments = $booking->payments()->where('status', 'paid')->get();
        $paidAmount = $payments->sum('amount');
        $dueAmount = max(0, $total - $paidAmount);

        // Validate payment amount
        if ((float)$validated['amount'] > $dueAmount) {
            return back()->withErrors(['amount' => "Payment amount cannot exceed due amount of BDT {$dueAmount}"]);
        }

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'hotel_id' => auth()->user()->hotel_id,
                'booking_id' => $booking->id,
                'created_by' => auth()->id(),
                'amount' => $validated['amount'],
                'currency' => 'BDT',
                'method' => $validated['method'],
                'type' => $validated['type'],
                'status' => 'paid',
                'reference' => $validated['reference'],
                'payment_date' => now()->toDateString(),
            ]);

            // Payment recorded. Booking paid amount is computed dynamically from payments.

            DB::commit();

            return redirect()
                ->back()
                ->with('success', "Payment of BDT {$validated['amount']} recorded successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Edit payment
     */
    // public function edit(Payment $payment)
    // {
    //     $this->authorizePayment($payment);

    //     // Only allow editing pending payments
    //     if ($payment->status !== 'pending') {
    //         return back()->withErrors('Only pending payments can be edited.');
    //     }

    //     $methods = ['cash', 'card', 'mobile_banking', 'bank_transfer'];
    //     $types = ['advance', 'balance', 'refund'];
    //     $statuses = ['pending', 'paid', 'failed', 'refunded'];

    //     return view('pages.erp.payments.edit', compact('payment', 'methods', 'types', 'statuses'));
    // }

    /**
     * Update payment
     */
    public function update(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return back()->withErrors('Only pending payments can be edited.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,mobile_banking,bank_transfer',
            'type' => 'required|in:advance,balance,refund',
            'reference' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,failed,refunded',
        ]);

        DB::beginTransaction();

        try {
            $oldAmount = $payment->amount;
            $payment->update($validated);

            // If status changed to paid, update booking paid amount
            if ($validated['status'] === 'paid') {
                $booking = $payment->booking;
                $total = 0;
                foreach ($booking->bookingRooms as $br) {
                    $checkIn = Carbon::parse($br->check_in);
                    $checkOut = Carbon::parse($br->check_out);
                    $nights = max(1, $checkIn->diffInDays($checkOut));
                    $total += $nights * (float)$br->price_per_night;
                }

                $paidAmount = $booking->payments()
                    ->where('status', 'paid')
                    ->where('id', '!=', $payment->id)
                    ->sum('amount');

                // Booking paid amount is calculated dynamically from payments; no stored update here.
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete payment
     */
    public function destroy(Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return back()->withErrors('Only pending payments can be deleted.');
        }

        DB::beginTransaction();

        try {
            $booking = $payment->booking;
            $payment->delete();

            // Recalculate booking paid amount
            $total = 0;
            foreach ($booking->bookingRooms as $br) {
                $checkIn = Carbon::parse($br->check_in);
                $checkOut = Carbon::parse($br->check_out);
                $nights = max(1, $checkIn->diffInDays($checkOut));
                $total += $nights * (float)$br->price_per_night;
            }

            // Booking paid amount is computed dynamically from payments; no stored update required.

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'paid') {
            return back()->withErrors('Only paid payments can be refunded.');
        }

        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Create refund payment record
            $refundPayment = Payment::create([
                'hotel_id' => $payment->hotel_id,
                'booking_id' => $payment->booking_id,
                'created_by' => auth()->id(),
                'amount' => $validated['refund_amount'],
                'currency' => $payment->currency,
                'method' => $payment->method,
                'type' => 'refund',
                'status' => 'paid',
                'reference' => 'REFUND-' . $payment->id . '-' . now()->timestamp,
                'payment_date' => now()->toDateString(),
            ]);

            // Update original payment status if fully refunded
            if ($validated['refund_amount'] >= $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            // Recalculate booking paid amount
            $booking = $payment->booking;
            $total = 0;
            foreach ($booking->bookingRooms as $br) {
                $checkIn = Carbon::parse($br->check_in);
                $checkOut = Carbon::parse($br->check_out);
                $nights = max(1, $checkIn->diffInDays($checkOut));
                $total += $nights * (float)$br->price_per_night;
            }

            $paidAmount = $booking->payments()
                ->where('status', 'paid')
                ->where('type', '!=', 'refund')
                ->sum('amount');

            $refundAmount = $booking->payments()
                ->where('type', 'refund')
                ->sum('amount');

            // Booking paid amount is computed dynamically from payments; no stored update required.

            DB::commit();

            return redirect()
                ->back()
                ->with('success', "Refund of BDT {$validated['refund_amount']} processed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process refund: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a simple invoice view for a booking
     */
    public function invoice(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paidAmount = $booking->payments()->where('status', 'paid')->sum('amount');
        $dueAmount = max(0, $total - $paidAmount);

        return view('pages.erp.payments.invoice', compact('booking', 'total', 'paidAmount', 'dueAmount'));
    }

    public function invoicePdf(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->load(['guest', 'bookingRooms.room', 'payments']);

        $pdf = Pdf::loadView(
            'pages.erp.invoices.pdf',
            [
                'booking' => $booking,
                'hotel' => $booking->hotel,
                'total' => $booking->total_amount,
                'paid' => $booking->paid_amount,
                'due' => $booking->due_amount,
            ]
        )->setPaper('a4');

        return $pdf->download(
            'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    /**
     * Generate a simple receipt for a payment
     */
    public function receipt(Payment $payment)
    {
        $this->authorizePayment($payment);
        return view('pages.erp.payments.receipt', compact('payment'));
    }

    /**
     * List all payments for hotel
     */
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $query = Payment::with(['booking.guest', 'hotel', 'createdBy'])
            ->where('hotel_id', $hotelId);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('payment_date', [$request->from, $request->to]);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        $statuses = ['pending', 'paid', 'failed', 'refunded'];
        $types = ['advance', 'balance', 'refund'];
        $methods = ['cash', 'card', 'mobile_banking', 'bank_transfer'];

        // Summary
        $summary = [
            'total_paid' => Payment::where('hotel_id', $hotelId)
                ->where('status', 'paid')
                ->where('type', '!=', 'refund')
                ->sum('amount'),
            'total_pending' => Payment::where('hotel_id', $hotelId)
                ->where('status', 'pending')
                ->sum('amount'),
            'total_failed' => Payment::where('hotel_id', $hotelId)
                ->where('status', 'failed')
                ->sum('amount'),
            'total_refunded' => Payment::where('hotel_id', $hotelId)
                ->where('type', 'refund')
                ->sum('amount'),
        ];

        return view('pages.erp.payments.index', compact('payments', 'statuses', 'types', 'methods', 'summary'));
    }

    /**
     * Authorization helpers
     */
    protected function authorizeBooking(Booking $booking)
    {
        if ($booking->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }
    }

    protected function authorizePayment(Payment $payment)
    {
        if ($payment->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }
    }

    // ──────────────────────────────────────────────
    //  SSLCommerz Payment Gateway Integration
    // ──────────────────────────────────────────────

    /**
     * Show the SSLCommerz online checkout page for a booking
     */
    public function sslcommerzCheckout(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paidAmount = $booking->payments()->where('status', 'paid')->where('type', '!=', 'refund')->sum('amount');
        $refundAmount = $booking->payments()->where('type', 'refund')->sum('amount');
        $paidAmount = max(0, $paidAmount - $refundAmount);
        $dueAmount = max(0, $total - $paidAmount);

        $types = ['advance', 'balance'];

        return view('pages.erp.payments.sslcommerz-checkout', compact('booking', 'total', 'paidAmount', 'dueAmount', 'types'));
    }

    /**
     * Initiate SSLCommerz Hosted Checkout payment
     */
    public function sslcommerzPay(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_type' => 'required|in:advance,balance',
        ]);

        // Calculate due amount
        $total = 0;
        foreach ($booking->bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paidAmount = $booking->payments()->where('status', 'paid')->where('type', '!=', 'refund')->sum('amount');
        $refundAmount = $booking->payments()->where('type', 'refund')->sum('amount');
        $paidAmount = max(0, $paidAmount - $refundAmount);
        $dueAmount = max(0, $total - $paidAmount);

        if ((float)$validated['amount'] > $dueAmount) {
            return back()->withErrors(['amount' => "Payment amount cannot exceed due amount of BDT {$dueAmount}"]);
        }

        $guest = $booking->guest;
        $hotel = $booking->hotel;
        $tranId = 'HTL' . $booking->id . '_' . uniqid();

        // Calculate stay details
        $firstRoom = $booking->bookingRooms->first();
        $checkInDate = $firstRoom ? Carbon::parse($firstRoom->check_in) : now();
        $checkOutDate = $firstRoom ? Carbon::parse($firstRoom->check_out) : now()->addDay();
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));

        $post_data = [];
        $post_data['total_amount'] = $validated['amount'];
        $post_data['currency'] = 'BDT';
        $post_data['tran_id'] = $tranId;

        // Customer Information
        $post_data['cus_name'] = $guest->full_name ?? 'Guest';
        $post_data['cus_email'] = $guest->email ?? 'guest@hotel.com';
        $post_data['cus_add1'] = $guest->address ?? 'N/A';
        $post_data['cus_add2'] = '';
        $post_data['cus_city'] = '';
        $post_data['cus_state'] = '';
        $post_data['cus_postcode'] = '';
        $post_data['cus_country'] = 'Bangladesh';
        $post_data['cus_phone'] = $guest->phone ?? '01700000000';
        $post_data['cus_fax'] = '';

        // Shipment Information (not applicable for hotel)
        $post_data['shipping_method'] = 'NO';
        $post_data['num_of_item'] = $booking->bookingRooms->count();
        $post_data['ship_name'] = $hotel->name ?? 'Hotel';
        $post_data['ship_add1'] = $hotel->address ?? 'N/A';
        $post_data['ship_add2'] = '';
        $post_data['ship_city'] = '';
        $post_data['ship_state'] = '';
        $post_data['ship_postcode'] = '';
        $post_data['ship_phone'] = $hotel->phone ?? '';
        $post_data['ship_country'] = 'Bangladesh';

        // Product Information — use travel-vertical profile
        $post_data['product_name'] = 'Hotel Room Booking #' . $booking->id;
        $post_data['product_category'] = 'hotel';
        $post_data['product_profile'] = 'travel-vertical';

        // Travel-vertical specific fields
        $post_data['hotel_name'] = $hotel->name ?? 'Hotel';
        $post_data['length_of_stay'] = $nights . ' night(s)';
        $post_data['check_in_time'] = $checkInDate->format('Y-m-d');
        $post_data['hotel_city'] = $hotel->city ?? 'Dhaka';

        // Pass booking & hotel context through extra value fields
        $post_data['value_a'] = $booking->id;           // booking_id
        $post_data['value_b'] = $booking->hotel_id;     // hotel_id
        $post_data['value_c'] = $validated['payment_type']; // advance or balance
        $post_data['value_d'] = auth()->id();           // created_by user

        // Create transaction record before initiating payment
        SslCommerzTransaction::create([
            'hotel_id' => $booking->hotel_id,
            'booking_id' => $booking->id,
            'transaction_id' => $tranId,
            'amount' => $validated['amount'],
            'currency' => 'BDT',
            'status' => 'Pending',
            'payment_type' => $validated['payment_type'],
        ]);

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            return back()->withErrors(['payment' => 'Failed to initiate payment. Please try again.']);
        }

        // Return payment form or redirect as needed
        return response($payment_options);
    }

    /**
     * SSLCommerz Success Callback
     */
    public function sslcommerzSuccess(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        $txn = SslCommerzTransaction::where('transaction_id', $tran_id)->first();

        if (!$txn) {
            return view('pages.erp.payments.sslcommerz-result', [
                'status' => 'error',
                'title' => 'Invalid Transaction',
                'message' => 'No transaction record found.',
                'transaction_id' => $tran_id,
                'booking_id' => null,
            ]);
        }

        if ($txn->status == 'Pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation) {
                // Update transaction status
                $txn->update(['status' => 'Processing']);

                // Create Payment record
                $bookingId = $request->input('value_a', $txn->booking_id);
                $hotelId = $request->input('value_b', $txn->hotel_id);
                $paymentType = $request->input('value_c', $txn->payment_type);
                $createdBy = $request->input('value_d');

                $payment = Payment::create([
                    'hotel_id' => $hotelId,
                    'booking_id' => $bookingId,
                    'created_by' => $createdBy,
                    'amount' => $amount,
                    'currency' => $currency,
                    'method' => 'sslcommerz',
                    'type' => $paymentType ?: 'balance',
                    'status' => 'paid',
                    'reference' => 'SSLCZ-' . $tran_id,
                    'transaction_id' => $tran_id,
                    'payment_date' => now()->toDateString(),
                ]);

                $txn->update(['payment_id' => $payment->id, 'status' => 'Complete']);

                return view('pages.erp.payments.sslcommerz-result', [
                    'status' => 'success',
                    'title' => 'Payment Successful!',
                    'message' => 'Your payment of BDT ' . number_format($amount, 2) . ' has been processed successfully.',
                    'transaction_id' => $tran_id,
                    'booking_id' => $bookingId,
                    'amount' => $amount,
                ]);
            }
        } elseif ($txn->status == 'Processing' || $txn->status == 'Complete') {
            return view('pages.erp.payments.sslcommerz-result', [
                'status' => 'success',
                'title' => 'Payment Already Processed',
                'message' => 'This transaction has already been successfully completed.',
                'transaction_id' => $tran_id,
                'booking_id' => $txn->booking_id,
                'amount' => $txn->amount,
            ]);
        }

        return view('pages.erp.payments.sslcommerz-result', [
            'status' => 'error',
            'title' => 'Validation Failed',
            'message' => 'Transaction could not be validated. Please contact support.',
            'transaction_id' => $tran_id,
            'booking_id' => $txn->booking_id,
        ]);
    }

    /**
     * SSLCommerz Fail Callback
     */
    public function sslcommerzFail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $txn = SslCommerzTransaction::where('transaction_id', $tran_id)->first();

        if ($txn && $txn->status == 'Pending') {
            $txn->update(['status' => 'Failed']);
        }

        return view('pages.erp.payments.sslcommerz-result', [
            'status' => 'failed',
            'title' => 'Payment Failed',
            'message' => 'Your payment could not be processed. Please try again or use a different payment method.',
            'transaction_id' => $tran_id,
            'booking_id' => $txn->booking_id ?? null,
        ]);
    }

    /**
     * SSLCommerz Cancel Callback
     */
    public function sslcommerzCancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $txn = SslCommerzTransaction::where('transaction_id', $tran_id)->first();

        if ($txn && $txn->status == 'Pending') {
            $txn->update(['status' => 'Canceled']);
        }

        return view('pages.erp.payments.sslcommerz-result', [
            'status' => 'canceled',
            'title' => 'Payment Canceled',
            'message' => 'You have canceled the payment. You can try again anytime.',
            'transaction_id' => $tran_id,
            'booking_id' => $txn->booking_id ?? null,
        ]);
    }

    /**
     * SSLCommerz IPN (Instant Payment Notification) Handler
     */
    public function sslcommerzIpn(Request $request)
    {
        if (!$request->input('tran_id')) {
            return response('Invalid Data', 400);
        }

        $tran_id = $request->input('tran_id');

        $txn = SslCommerzTransaction::where('transaction_id', $tran_id)->first();

        if (!$txn) {
            return response('Transaction not found', 404);
        }

        if ($txn->status == 'Pending') {
            $sslc = new SslCommerzNotification();
            $validation = $sslc->orderValidate($request->all(), $tran_id, $txn->amount, $txn->currency);

            if ($validation == TRUE) {
                $txn->update(['status' => 'Processing']);

                // Create Payment record if not already created by success callback
                if (!$txn->payment_id) {
                    $payment = Payment::create([
                        'hotel_id' => $txn->hotel_id,
                        'booking_id' => $txn->booking_id,
                        'created_by' => $request->input('value_d'),
                        'amount' => $txn->amount,
                        'currency' => $txn->currency,
                        'method' => 'sslcommerz',
                        'type' => $txn->payment_type ?: 'balance',
                        'status' => 'paid',
                        'reference' => 'SSLCZ-IPN-' . $tran_id,
                        'transaction_id' => $tran_id,
                        'payment_date' => now()->toDateString(),
                    ]);

                    $txn->update(['payment_id' => $payment->id, 'status' => 'Complete']);
                }

                return response('Transaction completed', 200);
            }
        } elseif ($txn->status == 'Processing' || $txn->status == 'Complete') {
            return response('Transaction already completed', 200);
        }

        return response('Invalid Transaction', 400);
    }
}
