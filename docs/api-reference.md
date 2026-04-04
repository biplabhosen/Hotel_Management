<![CDATA[# API & Route Reference

> **Web routes** are defined in `routes/web.php` — these serve Blade views and handle form submissions.  
> **API routes** are defined in `routes/api.php` — these return **JSON** and are prefixed with `/api`.  
> All web routes (except SSLCommerz callbacks) require session-based **authentication** via the `auth` middleware.

---

## Table of Contents

- [Web Routes](#web-routes)
  - [Authentication](#authentication)
  - [Dashboard](#dashboard)
  - [Bookings](#bookings)
  - [Rooms](#rooms)
  - [Payments](#payments)
  - [Checkout](#checkout)
  - [Housekeeping](#housekeeping)
  - [Hotels](#hotels)
  - [Users](#users)
  - [Dashboard API (Web)](#dashboard-api-web)
  - [SSLCommerz Payment Gateway](#sslcommerz-payment-gateway)
- [API Routes (routes/api.php)](#api-routes-routesapiphp)
  - [Authenticated User](#authenticated-user)
  - [Hotels List](#hotels-list)
  - [Guest API](#guest-api)

---

# Web Routes

All routes below are defined in `routes/web.php` and return Blade views unless noted otherwise.

## Authentication

Laravel UI authentication scaffolding provides the standard auth routes.

| Method | URI | Action | Name |
|---|---|---|---|
| GET | `/login` | Show login form | `login` |
| POST | `/login` | Process login | — |
| GET\|POST | `/logout` | Logout user | `logout` |
| GET | `/register` | Show registration form | `register` |
| POST | `/register` | Create account | — |
| GET | `/password/reset` | Show password reset request form | `password.request` |
| POST | `/password/email` | Send reset link email | `password.email` |
| GET | `/password/reset/{token}` | Show reset form | `password.reset` |
| POST | `/password/reset` | Reset password | `password.update` |

---

## Dashboard

| Method | URI | Controller | Description |
|---|---|---|---|
| GET | `/` | `HomeController@index` | Main dashboard with KPIs, charts, and recent bookings |

### Dashboard Data Includes:
- Total rooms, available rooms, occupied rooms
- Occupancy rate (%)
- Monthly revenue
- New bookings count (this month)
- Checkouts scheduled today
- 6-month booking trend chart data
- Booking status distribution (completed / in-process / pending)
- 6 most recent bookings

---

## Bookings

All booking routes are prefixed with `/booking` and use the `BookingController`.

| Method | URI | Action | Description |
|---|---|---|---|
| GET | `/booking` | `index` | List all bookings |
| GET | `/booking/create` | `create` | Show booking creation form |
| POST | `/booking/store` | `store` | Create a new booking |
| POST | `/booking/check-in/{booking}` | `checkIn` | Check-in a guest (validates date + 50% payment) |
| POST | `/booking/check-out/{booking}` | `checkOut` | Check-out a guest (validates 100% payment) |
| POST | `/booking/cancel/{booking}` | `cancel` | Cancel a booking |
| GET | `/booking/edit` | `edit` | Edit booking form |
| PUT | `/booking/update` | `update` | Update booking details |
| GET | `/booking/available` | `availableRooms` | Search available rooms by date range |
| GET | `/booking/calendar` | `calendar` | Room calendar view (FullCalendar) |
| GET | `/booking/calendar/api` | `apiCalendar` | Calendar events JSON |
| GET | `/booking/calendar/resources` | `calendarResources` | Calendar resources JSON |
| GET | `/booking/night-report` | `nightReport` | Night audit report |

### Named Routes

| Name | URI |
|---|---|
| `booking.cancel` | `/booking/cancel/{booking}` |
| `room.calendar` | `/booking/calendar` |
| `room.calendar.api` | `/booking/calendar/api` |
| `room.calendar.resources` | `/booking/calendar/resources` |
| `booking.night_report` | `/booking/night-report` |

---

## Rooms

All room routes are prefixed with `/room` and use the `RoomController`.

| Method | URI | Action | Description |
|---|---|---|---|
| GET | `/room` | `index` | List all rooms |
| GET | `/room/create` | `create` | Show room creation form |
| POST | `/room/store` | `store` | Create a new room |
| GET | `/room/edit/{room}` | `edit` | Edit room form |
| PUT | `/room/update/{room}` | `update` | Update room |
| GET | `/room/occupancy` | `occupency` | Room occupancy overview |
| GET | `/room/occupancy/ajax` | `occupencyAjax` | Room occupancy AJAX data |

---

## Payments

All payment routes are prefixed with `/payment` and use the `PaymentController`.

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/payment` | `payment.index` | List all payments (filterable) |
| GET | `/payment/booking/{booking}` | `booking.show` | Payment history for a booking |
| GET | `/payment/booking/{booking}/create` | `payment.create` | New payment form |
| POST | `/payment/booking/{booking}` | `payment.store` | Record a payment |
| GET | `/payment/booking/{booking}/invoice` | `payment.invoice` | Download PDF invoice |
| GET | `/payment/{payment}/receipt` | `payment.receipt` | View payment receipt |
| GET | `/payment/{payment}/edit` | `payment.edit` | Edit payment form |
| PUT | `/payment/{payment}` | `payment.update` | Update payment |
| DELETE | `/payment/{payment}` | `payment.destroy` | Delete payment (pending only) |
| POST | `/payment/{payment}/refund` | `payment.refund` | Process refund |

### Payment Filters (GET /payment)

| Parameter | Type | Description |
|---|---|---|
| `status` | string | Filter by status: `pending`, `paid`, `failed`, `refunded` |
| `type` | string | Filter by type: `advance`, `balance`, `refund` |
| `method` | string | Filter by method: `cash`, `card`, `mobile_banking`, `bank_transfer` |
| `from` | date | Start date for date range filter |
| `to` | date | End date for date range filter |

---

## Checkout

Checkout routes are prefixed with `/checkout` and use the `BookingCheckoutController`.

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/checkout` | `booking.checkout.index` | List active checked-in bookings |
| POST | `/checkout/{booking}` | `booking.checkout.submit` | Process checkout + create housekeeping tasks |

### Checkout Behavior

1. Booking status is set to `checked_out`
2. All booking rooms get `checked_out_at` timestamps
3. Each room status is set to `dirty`
4. A `checkout_cleaning` housekeeping task is auto-created for each room

---

## Housekeeping

Housekeeping routes are prefixed with `/housekeeping` and use the `HousekeepingController`.

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/housekeeping` | `housekeeping.index` | Task board (grouped by room) |
| GET | `/housekeeping/room/{room}` | `housekeeping.show` | Tasks for a specific room |
| POST | `/housekeeping/task/{task}/assign` | `housekeeping.assign` | Assign staff to a task |
| POST | `/housekeeping/task/{task}/complete` | `housekeeping.complete` | Mark task as completed |

### Task Lifecycle

```
pending → in_progress (staff assigned) → completed
```

When the last open task for a room is completed, the room status is automatically set to `available`.

---

## Hotels

Standard Laravel resource routes for hotel management.

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/hotels` | `hotels.index` | List hotels |
| GET | `/hotels/create` | `hotels.create` | New hotel form |
| POST | `/hotels` | `hotels.store` | Create hotel |
| GET | `/hotels/{hotel}` | `hotels.show` | Show hotel details |
| GET | `/hotels/{hotel}/edit` | `hotels.edit` | Edit hotel form |
| PUT/PATCH | `/hotels/{hotel}` | `hotels.update` | Update hotel |
| DELETE | `/hotels/{hotel}` | `hotels.destroy` | Delete hotel |

> **Note:** The `Hotel` model resolves route bindings by ID, slug, or hotel_name.

---

## Users

Standard Laravel resource routes with archive/restore support.

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/users` | `users.index` | List users |
| GET | `/users/create` | `users.create` | New user form |
| POST | `/users` | `users.store` | Create user |
| GET | `/users/{user}` | `users.show` | Show user |
| GET | `/users/{user}/edit` | `users.edit` | Edit user form |
| PUT/PATCH | `/users/{user}` | `users.update` | Update user |
| DELETE | `/users/{user}` | `users.destroy` | Delete user |
| PATCH | `/users/{user}/archive` | `users.archive` | Archive (soft disable) user |
| PATCH | `/users/{user}/restore` | `users.restore` | Restore archived user |

---

## Dashboard API (Web)

These JSON endpoints are defined in `routes/web.php` and used by the dashboard frontend widgets. They require session-based `auth`.

| Method | URI | Auth | Description |
|---|---|---|---|
| GET | `/api/occupancy/summary` | Required | Room occupancy summary (total, available, occupied) |
| GET | `/api/bookings/stats?months=6` | Required | Booking trends and status statistics |

### GET /api/occupancy/summary

Returns current occupancy data for the authenticated user's hotel.

### GET /api/bookings/stats

| Parameter | Type | Default | Description |
|---|---|---|---|
| `months` | int | 6 | Number of months to include |

**Response:**

```json
{
  "labels": ["Oct", "Nov", "Dec", "Jan", "Feb", "Mar"],
  "series": [12, 15, 20, 18, 22, 25],
  "bookingStats": {
    "completed": 10,
    "process": 5,
    "pending": 8
  }
}
```

---

## SSLCommerz Payment Gateway

### Authenticated Routes (Checkout Initiation)

| Method | URI | Name | Description |
|---|---|---|---|
| GET | `/payment/booking/{booking}/online` | `payment.sslcommerz.checkout` | SSLCommerz checkout page |
| POST | `/payment/booking/{booking}/sslcommerz-pay` | `payment.sslcommerz.pay` | Initiate SSLCommerz session |

### Callback Routes (Server-to-Server, No Auth/CSRF)

> **No authentication or CSRF protection** — these receive POST requests from SSLCommerz servers.

| Method | URI | Name | Description |
|---|---|---|---|
| POST | `/payment/sslcommerz/success` | `payment.sslcommerz.success` | Payment success callback |
| POST | `/payment/sslcommerz/fail` | `payment.sslcommerz.fail` | Payment failure callback |
| POST | `/payment/sslcommerz/cancel` | `payment.sslcommerz.cancel` | Payment cancellation callback |
| POST | `/payment/sslcommerz/ipn` | `payment.sslcommerz.ipn` | Instant Payment Notification |

### Security Note

The callback routes are excluded from CSRF verification in `VerifyCsrfToken` middleware. Payment validation is performed within the controller by:
1. Verifying the `tran_id` matches a pending `SslCommerzTransaction` record
2. Validating the amount matches
3. Checking the payment status from SSLCommerz

---

---

# API Routes (routes/api.php)

All routes below are defined in `routes/api.php`, return **JSON** responses, and are auto-prefixed with `/api` by Laravel.

> **Base URL:** `http://your-domain.com/api`

---

## Authenticated User

| Method | URI | Auth | Description |
|---|---|---|---|
| GET | `/api/user` | Sanctum | Returns the currently authenticated user |

**Auth:** Requires a valid Sanctum token (`Authorization: Bearer <token>`).

**Response:**

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "hotel_id": 1,
  "role_id": 1
}
```

---

## Hotels List

| Method | URI | Auth | Description |
|---|---|---|---|
| GET | `/api/hotel` | None | Returns all hotels |

**Response:**

```json
[
  {
    "id": 1,
    "hotel_name": "Hotel Florida",
    "slug": "hotel-florida",
    "email": "info@hotelflorida.com",
    "phone": "+880-1234567890",
    "address": "123 Main St, Dhaka"
  }
]
```

---

## Guest API

The Guest API is a **public-facing** JSON API (no authentication required) that enables guest booking portals, mobile apps, or third-party integrations. All routes are prefixed with `/api/guest` and use the `GuestApiController`.

### Hotel Information

| Method | URI | Description |
|---|---|---|
| GET | `/api/guest/hotel-by-slug/{slug}` | Find a hotel by its URL slug |
| GET | `/api/guest/hotels/{hotel}` | Get hotel details (by ID) |
| GET | `/api/guest/hotels/{hotel}/amenities` | List amenities for a hotel |
| GET | `/api/guest/hotels/{hotel}/room-types` | List room types with pricing and amenities |
| GET | `/api/guest/hotels/{hotel}/rooms` | List rooms with type details |
| GET | `/api/guest/hotels/{hotel}/rooms/{room}` | Get single room details |

### Availability & Booking

| Method | URI | Description |
|---|---|---|
| GET | `/api/guest/search-availability` | Search available rooms (by slug + dates) |
| POST | `/api/guest/hotels/{hotel}/availability` | Check room availability for dates |
| POST | `/api/guest/hotels/{hotel}/bookings` | Create a new booking |
| GET | `/api/guest/hotels/{hotel}/bookings` | Look up bookings by phone or email |
| GET | `/api/guest/hotels/{hotel}/bookings/{booking}/confirmation` | View booking confirmation |
| POST | `/api/guest/hotels/{hotel}/bookings/{booking}/cancel` | Cancel a booking |

---

### GET /api/guest/hotel-by-slug/{slug}

Look up a hotel by its URL-friendly slug.

**Example:** `GET /api/guest/hotel-by-slug/hotel-florida`

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "slug": "hotel-florida",
    "name": "Hotel Florida",
    "logo": null,
    "email": "info@hotelflorida.com",
    "phone": "+880-1234567890",
    "address": "123 Main St, Dhaka",
    "status": "active",
    "room_types_count": 5,
    "rooms_count": 30
  }
}
```

---

### GET /api/guest/hotels/{hotel}/room-types

List all active room types for a hotel with pricing and amenities.

| Parameter | Type | Default | Description |
|---|---|---|---|
| `include_inactive` | boolean | `false` | Include inactive room types |

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Standard Room",
      "code": "STD",
      "bed_type": "double",
      "bed_count": 1,
      "capacity": 2,
      "price_per_night": 3500.00,
      "description": "Comfortable standard room",
      "is_active": true,
      "amenities": [
        { "id": 1, "name": "Wi-Fi", "icon": "wifi" },
        { "id": 2, "name": "AC", "icon": "air-conditioner" }
      ]
    }
  ]
}
```

---

### GET /api/guest/hotels/{hotel}/rooms

List rooms for a hotel, optionally filtered by room type.

| Parameter | Type | Description |
|---|---|---|
| `room_type_id` | int (optional) | Filter by room type |

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "room_number": "101",
      "floor": 1,
      "status": "available",
      "room_type": {
        "id": 1,
        "name": "Standard Room",
        "code": "STD",
        "bed_type": "double",
        "bed_count": 1,
        "capacity": 2,
        "price_per_night": 3500.00,
        "amenities": []
      }
    }
  ]
}
```

---

### GET /api/guest/search-availability

Search available rooms across a hotel using its slug.

| Parameter | Type | Required | Description |
|---|---|---|---|
| `hotel_slug` | string | ✅ | Hotel slug |
| `check_in` | date | ✅ | Check-in date (≥ today) |
| `check_out` | date | ✅ | Check-out date (> check_in) |
| `room_type_id` | int | — | Filter by specific room type |
| `rooms_needed` | int | — | Number of rooms needed (default: 1) |

**Example:** `GET /api/guest/search-availability?hotel_slug=hotel-florida&check_in=2026-04-10&check_out=2026-04-12`

---

### POST /api/guest/hotels/{hotel}/availability

Check room availability for specific dates.

**Request Body:**

```json
{
  "check_in": "2026-04-10",
  "check_out": "2026-04-12",
  "room_type_id": 1,
  "rooms_needed": 2
}
```

**Validation:**

| Field | Rules |
|---|---|
| `check_in` | required, date, ≥ today |
| `check_out` | required, date, > check_in |
| `room_type_id` | optional, must belong to the hotel |
| `rooms_needed` | optional, min 1 |

**Response:**

```json
{
  "success": true,
  "data": {
    "check_in": "2026-04-10",
    "check_out": "2026-04-12",
    "nights": 2,
    "rooms_needed": 2,
    "available_rooms_count": 5,
    "can_fulfill": true,
    "rooms": [
      {
        "id": 1,
        "room_number": "101",
        "floor": 1,
        "room_type_id": 1,
        "room_type_name": "Standard Room",
        "price_per_night": 3500.00
      }
    ]
  }
}
```

---

### POST /api/guest/hotels/{hotel}/bookings

Create a new guest booking.

**Request Body:**

```json
{
  "full_name": "Jane Doe",
  "phone": "+880-1711234567",
  "email": "jane@example.com",
  "total_guests": 2,
  "check_in": "2026-04-10",
  "check_out": "2026-04-12",
  "room_ids": [1, 5]
}
```

**Validation:**

| Field | Rules |
|---|---|
| `full_name` | required, 2–255 chars |
| `phone` | required, 6–20 chars |
| `email` | optional, valid email |
| `total_guests` | required, min 1 |
| `check_in` | required, date ≥ today |
| `check_out` | required, date > check_in |
| `room_ids` | required, array of valid room IDs |

**Behavior:**
1. Creates or updates a `Guest` record (matched by hotel + phone)
2. Creates a `Booking` with status `reserved`
3. Creates `BookingRoom` entries for each room with dates and pricing
4. Validates room availability (no double-booking)
5. Validates rooms belong to the hotel and are not out-of-order

**Response (201 Created):**

```json
{
  "success": true,
  "message": "Booking created successfully.",
  "data": {
    "id": 42,
    "booking_reference": "BK-000042",
    "status": "reserved",
    "check_in": "2026-04-10",
    "check_out": "2026-04-12",
    "nights": 2,
    "total_guests": 2,
    "total_amount": 7000.00,
    "guest": {
      "id": 15,
      "full_name": "Jane Doe",
      "phone": "+880-1711234567",
      "email": "jane@example.com"
    },
    "rooms": [
      {
        "room_id": 1,
        "room_number": "101",
        "room_type_id": 1,
        "room_type_name": "Standard Room",
        "price_per_night": 3500.00,
        "check_in": "2026-04-10",
        "check_out": "2026-04-12"
      }
    ],
    "created_at": "2026-04-04 10:30:00"
  }
}
```

---

### GET /api/guest/hotels/{hotel}/bookings

Look up bookings by guest phone or email.

| Parameter | Type | Required | Description |
|---|---|---|---|
| `phone` | string | * | Guest phone number |
| `email` | email | * | Guest email address |
| `per_page` | int | — | Results per page (1–50, default: 10) |

> \* At least one of `phone` or `email` is required.

**Response:**

```json
{
  "success": true,
  "data": [ /* array of booking objects */ ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 3
  }
}
```

---

### GET /api/guest/hotels/{hotel}/bookings/{booking}/confirmation

View booking confirmation details. Requires guest verification.

| Parameter | Type | Required | Description |
|---|---|---|---|
| `phone` | string | * | Guest phone number |
| `email` | email | * | Guest email address |

> \* At least one must match the booking's guest record.

**Error (403):** `"Guest verification failed."` if neither phone nor email matches.

---

### POST /api/guest/hotels/{hotel}/bookings/{booking}/cancel

Cancel a booking. Requires guest verification.

| Parameter | Type | Required | Description |
|---|---|---|---|
| `phone` | string | * | Guest phone number |
| `email` | email | * | Guest email address |

> \* At least one must match the booking's guest record.

**Rules:**
- Cannot cancel bookings with status `checked_out` or `cancelled`
- Sets booking status to `cancelled`

**Response:**

```json
{
  "success": true,
  "message": "Booking cancelled successfully.",
  "data": { /* booking object with updated status */ }
}
```

---

## Error Responses

All API endpoints return consistent error responses:

### Validation Error (422)

```json
{
  "message": "The check in field is required.",
  "errors": {
    "check_in": ["The check in field is required."]
  }
}
```

### Not Found (404)

```json
{
  "success": false,
  "message": "Room not found."
}
```

### Authorization Failed (403)

```json
{
  "success": false,
  "message": "Guest verification failed."
}
```

### Business Logic Error (422)

```json
{
  "success": false,
  "message": "This booking cannot be cancelled."
}
```
]]>
