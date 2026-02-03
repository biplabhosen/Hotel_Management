<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoNoShowBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark bookings as no_show if guest did not check in within grace period (next day 12:00)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $bookings = Booking::where('status', 'reserved')
            ->whereHas('bookingRooms', function ($q) use ($yesterday) {
                $q->whereDate('check_in', $yesterday);
            })
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            // find a booking room that has check_in == yesterday
            $br = $booking->bookingRooms()->whereDate('check_in', $yesterday)->first();
            if (! $br) {
                continue;
            }

            $deadline = Carbon::parse($br->check_in)->addDay()->setTime(12, 0, 0);

            if (Carbon::now()->greaterThanOrEqualTo($deadline)) {
                $booking->update(['status' => 'no_show']);
                $count++;
                Log::info("Booking {$booking->id} marked as no_show by AutoNoShowBookings");
            }
        }

        $this->info("Marked {$count} bookings as no_show.");

        return 0;
    }
}
