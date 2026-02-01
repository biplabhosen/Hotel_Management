<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $hotelId = auth()->user()->hotel_id;

        // Total rooms and available as of today
        $rooms = \App\Models\Room::with('bookingRooms.booking')->where('hotel_id', $hotelId)->get();
        $totalRooms = $rooms->count();
        $availableRooms = $rooms->filter(fn($r) => $r->status === 'available')->count();
        $occupiedRooms = $rooms->filter(fn($r) => $r->status === 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

        // Revenue & bookings for the current month
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $now = \Carbon\Carbon::now();

        $revenueMonth = \App\Models\Payment::where('hotel_id', $hotelId)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->sum('amount');

        $newBookingsCount = \App\Models\Booking::where('hotel_id', $hotelId)
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->count();

        // Checkouts today (booking rooms store check_out)
        $checkoutsToday = \App\Models\BookingRoom::whereHas('booking', fn($q) => $q->where('hotel_id', $hotelId))
            ->whereDate('check_out', \Carbon\Carbon::today())
            ->count();

        // Bookings per month for the last 6 months (labels + series)
        $labels = [];
        $series = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = \Carbon\Carbon::now()->subMonths($i);
            $labels[] = $m->format('M');
            $series[] = \App\Models\Booking::where('hotel_id', $hotelId)
                ->whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)
                ->count();
        }

        // Booking status counts for the current month (used by the Booking Statistics pie)
        $statusCounts = \App\Models\Booking::where('hotel_id', $hotelId)
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $bookingStats = [
            'completed' => $statusCounts['checked_out'] ?? 0,
            'process'   => $statusCounts['checked_in'] ?? 0,
            'pending'   => ($statusCounts['reserved'] ?? 0) + ($statusCounts['pending'] ?? 0),
        ];

        // Recent bookings (latest 6)
        $recentBookings = \App\Models\Booking::where('hotel_id', $hotelId)
            ->with(['guest', 'bookingRooms.room.roomType'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('pages.erp.index', compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'occupancyRate',
            'revenueMonth',
            'newBookingsCount',
            'checkoutsToday',
            'labels',
            'series',
            'recentBookings',
            'bookingStats'
        ));
    }

    /**
     * API endpoint returning booking statistics used by the dashboard SPA
     */
    public function apiBookingStats(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        $months = max(1, (int) $request->query('months', 6));

        $labels = [];
        $series = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = \Carbon\Carbon::now()->subMonths($i);
            $labels[] = $m->format('M');
            $series[] = \App\Models\Booking::where('hotel_id', $hotelId)
                ->whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)
                ->count();
        }

        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $now = \Carbon\Carbon::now();
        $statusCounts = \App\Models\Booking::where('hotel_id', $hotelId)
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $bookingStats = [
            'completed' => $statusCounts['checked_out'] ?? 0,
            'process'   => $statusCounts['checked_in'] ?? 0,
            'pending'   => ($statusCounts['reserved'] ?? 0) + ($statusCounts['pending'] ?? 0),
        ];

        return response()->json([
            'labels' => $labels,
            'series' => $series,
            'bookingStats' => $bookingStats,
        ]);
    }
}
