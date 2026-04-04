<![CDATA[# Auto No-Show / Auto-Cancel System

Automatically marks unattended reservations as **no-show** after a configurable grace period.

---

## How It Works

| Condition | Value |
|---|---|
| **Booking status** | `reserved` |
| **Check-in date** | Yesterday (i.e., the guest was expected but did not arrive) |
| **Grace period** | 12 hours into the next day (command runs at 12:05 noon) |
| **Resulting status** | `no_show` |

When a booking remains in `reserved` status and the room's `check_in` date was **yesterday**, the system automatically marks it as `no_show` if the guest has not checked in by **12:00 noon** the next day.

---

## Implementation

### Artisan Command

**File:** `app/Console/Commands/AutoNoShowBookings.php`

```bash
php artisan bookings:auto-no-show
```

### Scheduler Registration

**File:** `app/Console/Kernel.php`

The command is scheduled to run daily at **12:05**:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('bookings:auto-no-show')->dailyAt('12:05');
}
```

---

## Enabling the Scheduler

### Linux / macOS (Cron)

Add this entry to your crontab (`crontab -e`):

```
* * * * * cd /path/to/Hotel_Management && php artisan schedule:run >> /dev/null 2>&1
```

### Windows (Task Scheduler)

1. Open **Task Scheduler**
2. Create a new task that runs every **1 minute**
3. Set the action to:
   ```
   php C:\path\to\Hotel_Management\artisan schedule:run
   ```

---

## Testing

### Manual Test

```bash
php artisan bookings:auto-no-show
```

### Quick Development Test

1. Create a booking with:
   - `check_in` = yesterday's date
   - `status` = `reserved`
2. Run the command:
   ```bash
   php artisan bookings:auto-no-show
   ```
3. Verify the booking now has `status = no_show`

---

## Notes

- The command only affects bookings in `reserved` status — `checked_in`, `checked_out`, and `cancelled` bookings are ignored
- The 12:05 schedule (rather than 12:00) adds a 5-minute buffer to avoid any timezone edge cases
- Each execution logs the number of bookings affected
]]>
