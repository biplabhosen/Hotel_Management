<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            return back()->withErrors(['amount' => "Payment amount cannot exceed due amount of Rs. {$dueAmount}"]);
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

            // Update booking paid amount
            $newPaidAmount = $paidAmount + (float)$validated['amount'];
            if ($newPaidAmount >= $total) {
                $booking->update(['paid_amount' => $total]);
            } else {
                $booking->update(['paid_amount' => $newPaidAmount]);
            }

            DB::commit();

            return redirect()
                ->route('booking.show', $booking)
                ->with('success', "Payment of Rs. {$validated['amount']} recorded successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Edit payment
     */
    public function edit(Payment $payment)
    {
        $this->authorizePayment($payment);

        // Only allow editing pending payments
        if ($payment->status !== 'pending') {
            return back()->withErrors('Only pending payments can be edited.');
        }

        $methods = ['cash', 'card', 'mobile_banking', 'bank_transfer'];
        $types = ['advance', 'balance', 'refund'];
        $statuses = ['pending', 'paid', 'failed', 'refunded'];

        return view('pages.erp.payments.edit', compact('payment', 'methods', 'types', 'statuses'));
    }

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

                $newPaidAmount = $paidAmount + $validated['amount'];
                $booking->update(['paid_amount' => min($newPaidAmount, $total)]);
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

            $paidAmount = $booking->payments()
                ->where('status', 'paid')
                ->sum('amount');

            $booking->update(['paid_amount' => $paidAmount]);

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

            $finalPaid = max(0, $paidAmount - $refundAmount);
            $booking->update(['paid_amount' => $finalPaid]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', "Refund of Rs. {$validated['refund_amount']} processed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process refund: ' . $e->getMessage()]);
        }
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
}
