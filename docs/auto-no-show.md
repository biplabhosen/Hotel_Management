Auto No-Show / Auto Cancel

- Bookings that remain in **reserved** status and whose room's `check_in` date was **yesterday** will be automatically marked **`no_show`** if the guest has not checked in by **12:00 (noon)** the next day.
- Implemented via the Artisan command `bookings:auto-no-show` (see `app/Console/Commands/AutoNoShowBookings.php`). The command is scheduled in `app/Console/Kernel.php` to run daily at **12:05**.

How to enable the scheduler (important):
- Linux/macOS: add this cron entry to run Laravel scheduler every minute:

  * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

- Windows (Task Scheduler): create a task that runs `php C:\path\to\artisan schedule:run` every minute.

Testing:
- You can test manually with `php artisan bookings:auto-no-show`.
- To test quickly for development, create a booking with `check_in` = yesterday and `status` = `reserved`, run the command and verify the booking has status `no_show` afterwards.
