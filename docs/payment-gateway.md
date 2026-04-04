<![CDATA[# Payment System & SSLCommerz Gateway Documentation

This document covers the complete payment system architecture, including manual payments and the SSLCommerz online payment gateway integration.

---

## Table of Contents

- [Payment Architecture Overview](#payment-architecture-overview)
- [Manual Payment Flow](#manual-payment-flow)
- [SSLCommerz Integration](#sslcommerz-integration)
- [Configuration](#configuration)
- [Payment Flow Diagrams](#payment-flow-diagrams)
- [Security Considerations](#security-considerations)
- [Troubleshooting](#troubleshooting)

---

## Payment Architecture Overview

The system supports two payment channels:

| Channel | Method | Use Case |
|---|---|---|
| **Manual** | Cash, card, mobile banking, bank transfer | Front-desk payments recorded by staff |
| **Online** | SSLCommerz gateway | Guest self-service payments via web |

### Data Model

```
Payment (manual or online)
├── hotel_id        → Hotel scope
├── booking_id      → Associated booking
├── created_by      → Staff who recorded (null for online)
├── amount          → Transaction amount (BDT)
├── method          → cash / card / mobile_banking / bank_transfer
├── type            → advance / balance / refund
├── status          → pending / paid / failed / refunded
├── reference       → Manual reference number
├── transaction_id  → SSLCommerz tran_id (for online)
└── payment_date    → Date of payment

SslCommerzTransaction (online only)
├── transaction_id  → Unique tran_id sent to SSLCommerz
├── amount          → Expected amount
├── status          → Pending / Processing / VALID / FAILED / CANCELLED
├── payment_type    → Gateway-reported payment type
└── payment_id      → FK to payments table (linked after success)
```

### Payment Types

| Type | When Used | Description |
|---|---|---|
| `advance` | Before/at check-in | Deposit or partial payment |
| `balance` | At check-out | Remaining amount to settle the bill |
| `refund` | After cancellation | Tracked as separate negative transaction |

### Payment Methods

| Method | Code | Description |
|---|---|---|
| Cash | `cash` | Cash payment at front desk |
| Card | `card` | Credit/debit card (POS or online) |
| Mobile Banking | `mobile_banking` | bKash, Nagad, Rocket, etc. |
| Bank Transfer | `bank_transfer` | Wire transfer |

---

## Manual Payment Flow

### Recording a Payment

1. Navigate to **Payment → Booking → Record Payment**
2. Fill in the payment form:
   - **Amount** (validated: must not exceed due amount)
   - **Method** (cash, card, mobile banking, bank transfer)
   - **Type** (advance or balance)
   - **Reference** (optional transaction reference)
3. System creates a `Payment` record with:
   - `status = paid`
   - `created_by = authenticated user ID`
   - `payment_date = today`
4. Booking's computed `paid_amount` is automatically updated

### Editing a Payment

- Only **pending** payments can be edited
- After editing, the booking's `paid_amount` is recalculated

### Deleting a Payment

- Only **pending** payments can be deleted (soft-deleted for audit trail)
- After deletion, the booking's `paid_amount` is recalculated

### Processing a Refund

1. Navigate to the booking's payment details
2. Click **Refund** on a paid payment
3. Enter refund amount and reason
4. System creates a new `Payment` record:
   - `type = refund`
   - `status = paid`
   - `amount = refund amount`
5. If the refund equals the original amount, the original payment's status changes to `refunded`
6. The booking's net `paid_amount` is recalculated

---

## SSLCommerz Integration

### Overview

[SSLCommerz](https://sslcommerz.com) is a Bangladeshi payment gateway supporting:
- Visa, MasterCard, Amex
- bKash, Nagad, Rocket (mobile banking)
- Internet banking (multiple banks)
- Installment payments

### Architecture

```
┌───────────────────────────────────────────────────────────────┐
│                        Guest Browser                          │
│                                                               │
│  1. Guest clicks "Pay Online" on checkout page                │
│  2. Form submitted to /payment/booking/{id}/sslcommerz-pay    │
└──────────────────────────┬────────────────────────────────────┘
                           │ POST
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                     Laravel Application                      │
│                                                              │
│  PaymentController@sslcommerzPay                             │
│  ├─ Creates SslCommerzTransaction (status: Pending)          │
│  ├─ Sends API request to SSLCommerz with:                    │
│  │   - store_id, store_passwd                                │
│  │   - total_amount, currency                                │
│  │   - tran_id (unique)                                      │
│  │   - success/fail/cancel/IPN callback URLs                 │
│  │   - customer info (name, email, phone)                    │
│  └─ Redirects guest to SSLCommerz payment page               │
└──────────────────────────┬───────────────────────────────────┘
                           │ Redirect
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                    SSLCommerz Gateway                         │
│                                                              │
│  Guest completes payment on SSLCommerz-hosted page           │
└──────────────────────────┬───────────────────────────────────┘
                           │ POST (server-to-server)
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                   Callback Handlers                          │
│                                                              │
│  /payment/sslcommerz/success → sslcommerzSuccess()           │
│  /payment/sslcommerz/fail    → sslcommerzFail()              │
│  /payment/sslcommerz/cancel  → sslcommerzCancel()            │
│  /payment/sslcommerz/ipn     → sslcommerzIpn()               │
│                                                              │
│  On success:                                                 │
│  ├─ Validate tran_id against SslCommerzTransaction           │
│  ├─ Verify amount matches                                    │
│  ├─ Create Payment record (status: paid, method: card)       │
│  ├─ Update SslCommerzTransaction (status: VALID)             │
│  └─ Redirect guest to booking payment page                   │
└──────────────────────────────────────────────────────────────┘
```

### Routes

#### Authenticated (Guest-Facing)

```php
// Checkout page showing payment options
Route::get('booking/{booking}/online', 'sslcommerzCheckout')
    ->name('payment.sslcommerz.checkout');

// Initiate SSLCommerz session
Route::post('booking/{booking}/sslcommerz-pay', 'sslcommerzPay')
    ->name('payment.sslcommerz.pay');
```

#### Callback (Server-to-Server, No Auth/CSRF)

```php
Route::post('/success', 'sslcommerzSuccess')->name('payment.sslcommerz.success');
Route::post('/fail', 'sslcommerzFail')->name('payment.sslcommerz.fail');
Route::post('/cancel', 'sslcommerzCancel')->name('payment.sslcommerz.cancel');
Route::post('/ipn', 'sslcommerzIpn')->name('payment.sslcommerz.ipn');
```

### CSRF Exemption

The four callback routes are exempted from CSRF verification in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'payment/sslcommerz/success',
    'payment/sslcommerz/fail',
    'payment/sslcommerz/cancel',
    'payment/sslcommerz/ipn',
];
```

This is necessary because SSLCommerz sends server-to-server POST requests that cannot include a CSRF token.

---

## Configuration

### Environment Variables

Add these to your `.env` file:

```dotenv
# SSLCommerz Credentials
SSLCZ_STORE_ID=your_store_id
SSLCZ_STORE_PASSWORD=your_store_password

# Set to true for sandbox testing, false for production
SSLCZ_TESTMODE=true

# Callback URLs (must be publicly accessible)
SSLCZ_SUCCESS_URL=https://yourdomain.com/payment/sslcommerz/success
SSLCZ_FAILED_URL=https://yourdomain.com/payment/sslcommerz/fail
SSLCZ_CANCEL_URL=https://yourdomain.com/payment/sslcommerz/cancel
SSLCZ_IPN_URL=https://yourdomain.com/payment/sslcommerz/ipn
```

### Sandbox vs Production

| Setting | Sandbox | Production |
|---|---|---|
| `SSLCZ_TESTMODE` | `true` | `false` |
| API Endpoint | `https://sandbox.sslcommerz.com` | `https://securepay.sslcommerz.com` |
| Store Credentials | Test credentials from SSLCommerz | Production credentials |

### Local Development with ngrok

For local development, SSLCommerz callbacks need a public URL. Use [ngrok](https://ngrok.com/):

```bash
ngrok http 8000
```

Then update the callback URLs in `.env` with the ngrok HTTPS URL:

```dotenv
SSLCZ_SUCCESS_URL=https://your-random-id.ngrok-free.dev/payment/sslcommerz/success
# ... etc.
```

> **Important:** Also update `APP_URL` and session/cookie settings for ngrok:
> ```dotenv
> SESSION_SECURE_COOKIE=true
> SESSION_SAME_SITE=none
> ```

---

## Payment Flow Diagrams

### Check-in with Payment Validation

```
Guest arrives
    │
    ▼
Check booking status = "reserved"
    │
    ▼
Check arrival date = today ───── NO ──→ Error: "Cannot check in, date mismatch"
    │
   YES
    ▼
Check paid_amount ≥ 50% total ── NO ──→ Error: "Minimum 50% advance required"
    │                                    (shows total, paid, and due amounts)
   YES
    ▼
✅ Set status = "checked_in"
   Set checked_in_at = now()
```

### Check-out with Payment Validation

```
Guest departs
    │
    ▼
Check booking status = "checked_in"
    │
    ▼
Check paid_amount = 100% total ── NO ──→ Error: "Full payment settlement required"
    │                                     (shows outstanding balance)
   YES
    ▼
✅ Set status = "checked_out"
   Set checked_out_at = now()
   Set rooms to "dirty"
   Create housekeeping tasks
```

---

## Security Considerations

### Payment Validation

1. **Amount validation**: Payment amounts are validated against the booking's remaining due balance — overpayment is prevented
2. **Hotel scoping**: All payment operations verify `hotel_id` matches the authenticated user's hotel
3. **Staff attribution**: Every manual payment records the `created_by` user ID
4. **Soft deletes**: Payments are soft-deleted (never permanently removed) for audit compliance

### SSLCommerz Callback Security

1. **Transaction matching**: Callbacks are validated against existing `SslCommerzTransaction` records by `tran_id`
2. **Amount verification**: The received amount is compared against the expected amount
3. **Idempotency**: Duplicate callbacks for the same `tran_id` are handled gracefully
4. **No auth required**: Callbacks bypass authentication and CSRF (server-to-server), but transaction validation provides equivalent security

### Sensitive Data

- **Never store** card numbers or CVVs — SSLCommerz handles all PCI-DSS compliance
- Store only the `tran_id` and gateway response status
- Keep `SSLCZ_STORE_PASSWORD` in `.env` (never commit to version control)

---

## Troubleshooting

### Common Issues

| Issue | Cause | Solution |
|---|---|---|
| Callback returns 419 | CSRF not excluded | Add callback URLs to `VerifyCsrfToken::$except` |
| Callback returns 404 | Wrong URL in .env | Verify callback URLs match route definitions |
| "Transaction not found" | Stale tran_id or DB mismatch | Check `ssl_commerz_transactions` table |
| Payment not reflected | IPN not received | Check server logs; ensure public URL is accessible |
| Redirect loop after payment | Session/cookie mismatch | Set `SESSION_SECURE_COOKIE=true` and `SESSION_SAME_SITE=none` for ngrok |

### Debugging

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Real-time logs**: `php artisan pail` (or `composer dev` includes it)
3. **SSLCommerz dashboard**: Check transaction status at https://sandbox.sslcommerz.com/manage/
4. **Test the IPN**: Use tools like Postman to simulate callback POSTs
]]>
