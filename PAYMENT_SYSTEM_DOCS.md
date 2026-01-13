# Check-in/Check-out & Payment System - Implementation Summary

## ✅ Database Migration
**File:** `database/migrations/2026_01_04_033204_create_payments_table.php`

Enhanced payment table with:
- Foreign keys for `hotel_id`, `booking_id`, `created_by`
- `currency` field (default: BDT)
- `type` enum: `advance`, `balance`, `refund`
- `status` enum: `pending`, `paid`, `failed`, `refunded`
- `method` enum: `cash`, `card`, `mobile_banking`, `bank_transfer`
- `reference` field for transaction tracking
- `payment_date` date field
- `softDeletes` for audit trail
- Composite index on `[hotel_id, booking_id]`

**File:** `database/migrations/2026_01_13_add_checkin_checkout_to_bookings.php`

Added to bookings table:
- `checked_in_at` - datetime when guest checks in
- `checked_out_at` - datetime when guest checks out

---

## ✅ Models

### Payment Model
**File:** `app/Models/Payment.php`

**Relationships:**
- `hotel()` - belongsTo Hotel
- `booking()` - belongsTo Booking
- `createdBy()` - belongsTo User (staff who created payment)

**Scopes:**
- `pending()`, `paid()`, `failed()`, `refunded()`
- `advance()`, `balance()`, `refundType()`
- `byHotel()`, `byBooking()`, `byDateRange()`, `byMethod()`

**Accessors:**
- `is_paid`, `is_pending`, `is_failed`, `is_refunded`
- `is_advance`, `is_balance`, `is_refund_type`

### Booking Model
**File:** `app/Models/Booking.php`

**Added:**
- `payments()` relationship - hasMany Payment
- Cast for `checked_in_at` and `checked_out_at`

---

## ✅ Controllers

### PaymentController
**File:** `app/Http/Controllers/PaymentController.php`

**Methods:**
1. `index()` - List all payments with filters (status, type, method, date range)
   - Shows summary: total paid, pending, failed, refunded

2. `show(Booking)` - Display payment history for a booking
   - Shows total, paid, and due amounts
   - Payment progress percentage
   - List of all payment records

3. `create(Booking)` - Show payment form
   - Shows due amount, payment methods, and payment types

4. `store(Booking)` - Record new payment
   - Validates amount ≤ due amount
   - Creates payment record with `created_by` tracking
   - Automatically updates booking `paid_amount`

5. `edit(Payment)` - Edit pending payments only

6. `update(Payment)` - Update payment details
   - Recalculates booking paid amount

7. `destroy(Payment)` - Delete pending payments
   - Recalculates booking paid amount

8. `refund(Payment)` - Process refunds
   - Creates separate refund transaction
   - Updates payment status if fully refunded
   - Recalculates net paid amount

### BookingController
**File:** `app/Http/Controllers/BookingController.php`

**Enhanced Methods:**

1. `checkIn(Booking)` - Check-in with payment validation
   - ✓ Validates arrival date matches today
   - ✓ Requires minimum 50% advance payment
   - ✓ Shows detailed error messages with dates and amounts
   - ✓ Sets `checked_in_at` timestamp

2. `checkOut(Booking)` - Check-out with full payment validation
   - ✓ Validates departure date matches today
   - ✓ Requires 100% payment settlement
   - ✓ Shows outstanding balance if incomplete
   - ✓ Sets `checked_out_at` timestamp

---

## ✅ Routes

**File:** `routes/web.php`

```php
Route::middleware('auth')->prefix('payment')->controller(PaymentController::class)->group(function(){
    Route::get('/', 'index')->name('payment.index');
    Route::get('booking/{booking}', 'show')->name('booking.show');
    Route::get('booking/{booking}/create', 'create')->name('payment.create');
    Route::post('booking/{booking}', 'store')->name('payment.store');
    Route::get('{payment}/edit', 'edit')->name('payment.edit');
    Route::put('{payment}', 'update')->name('payment.update');
    Route::delete('{payment}', 'destroy')->name('payment.destroy');
    Route::post('{payment}/refund', 'refund')->name('payment.refund');
});
```

---

## ✅ Views

**All in:** `resources/views/pages/erp/payments/`

### 1. `index.blade.php` - All Payments
- Summary statistics (total paid, pending, failed, refunded)
- Filter by status, type, method, date range
- Table with booking details, amount, method, status
- Links to booking details
- Pagination

### 2. `show.blade.php` - Booking Payment Details
- Guest information
- Total vs Paid vs Due amounts
- Payment completion percentage
- Room details
- Full payment history table with:
  - Date, amount, method, reference, status, notes
  - Action buttons (Edit, Delete, Refund)
  - Refund modal with amount and reason
- Call-to-action to record payment if due exists

### 3. `create.blade.php` - Record Payment
- Booking summary
- Payment form with fields:
  - Amount (with validation for max due)
  - Method (cash, card, mobile banking, bank transfer)
  - Type (advance, balance, refund)
  - Transaction reference
- Booking details sidebar
- Payment tips sidebar

---

## 🔄 Payment Flow

### Recording Payment
1. User clicks "Record Payment" button
2. Fills form with amount, method, type, reference
3. System validates:
   - Amount ≤ due amount
   - Required fields present
4. Creates payment record with `created_by` tracking
5. Automatically updates booking `paid_amount`
6. Redirects to booking payment details

### Check-in Process
1. Booking must be in `reserved` status
2. ✓ Check arrival date = today
3. ✓ Check advance payment ≥ 50% of total
4. If conditions met → status becomes `checked_in`
5. Sets `checked_in_at` timestamp

### Check-out Process
1. Booking must be in `checked_in` status
2. ✓ Check departure date = today
3. ✓ Check full payment = 100% of total
4. If conditions met → status becomes `checked_out`
5. Sets `checked_out_at` timestamp

### Refund Process
1. Only paid payments can be refunded
2. Creates separate refund transaction (type: `refund`)
3. Original payment marked as `refunded` if fully refunded
4. Net paid amount automatically recalculated

---

## 📊 Key Features

✅ **Complete Payment Tracking**
- Multiple payment methods supported
- Transaction references for audit trail
- Staff attribution (created_by)
- Soft deletes for audit history

✅ **Payment Types**
- **Advance:** Initial payment before check-in
- **Balance:** Final payment at check-out
- **Refund:** Refund transactions tracked separately

✅ **Smart Check-in/Check-out**
- Automatic date validation
- Payment validation with clear error messages
- Prevents over-payment
- Prevents under-payment at check-out
- Timestamps for audit trail

✅ **Flexible Payment Management**
- Edit pending payments
- Delete pending payments
- Process refunds with reasons
- View complete payment history

✅ **Dashboard & Reporting**
- Payment summary statistics
- Filter by status, method, date
- Booking-wise payment tracking
- Payment progress percentage

---

## 📋 Database Structure Summary

### Payments Table
```
id | hotel_id | booking_id | created_by | amount | currency | method | type | status | reference | payment_date | created_at | updated_at | deleted_at
```

### Bookings Table (Enhanced)
```
... | checked_in_at | checked_out_at | ...
```

---

## 🔐 Security

- Authorization checks on all controller methods
- User can only access payments/bookings from their hotel
- Soft deletes maintain audit trail
- Transaction tracking with staff attribution
- Validation on all financial transactions

---

## 🎯 Next Steps (Optional Enhancements)

- [ ] Email notifications for payment reminders
- [ ] Payment receipt generation (PDF)
- [ ] Automated payment reconciliation reports
- [ ] Payment gateway integration (Stripe, SSLCommerz)
- [ ] Installment payment support
- [ ] Invoice generation
- [ ] Payment method restrictions per booking
- [ ] Commission/tax calculation
