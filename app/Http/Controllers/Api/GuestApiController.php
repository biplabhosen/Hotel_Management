<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuestApiController extends Controller
{
    public function hotelBySlug(string $slug)
    {
        return $this->hotelInfo(Hotel::findByTenantSlugOrFail($slug));
    }

    public function hotelInfo(Hotel $hotel)
    {
        $hotel->loadCount([
            'roomTypes as room_types_count' => fn($q) => $q->where('is_active', true),
            'rooms as rooms_count',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $hotel->id,
                'slug' => $hotel->slug,
                'name' => $hotel->name,
                'logo' => $hotel->logo,
                'email' => $hotel->email,
                'phone' => $hotel->phone,
                'address' => $hotel->address,
                'status' => $hotel->status,
                'room_types_count' => $hotel->room_types_count,
                'rooms_count' => $hotel->rooms_count,
            ],
        ]);
    }

    public function searchAvailability(Request $request)
    {
        $hotelSlug = $request->query('hotel_slug');

        if (empty($hotelSlug)) {
            return response()->json([
                'success' => false,
                'message' => 'hotel_slug query parameter is required.',
            ], 422);
        }

        return $this->availabilityCheck(Hotel::findByTenantSlugOrFail($hotelSlug), $request);
    }

    public function amenities(Hotel $hotel)
    {
        $amenities = Amenity::query()
            ->select('amenities.id', 'amenities.name', 'amenities.icon')
            ->join('amenity_room', 'amenity_room.amenity_id', '=', 'amenities.id')
            ->join('rooms', 'rooms.id', '=', 'amenity_room.room_id')
            ->where('rooms.hotel_id', $hotel->id)
            ->distinct()
            ->orderBy('amenities.name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $amenities,
        ]);
    }

    public function roomTypes(Hotel $hotel, Request $request)
    {
        $includeInactive = $request->boolean('include_inactive', false);

        $roomTypesQuery = RoomType::query()
            ->where('hotel_id', $hotel->id)
            ->orderBy('price_per_night');

        if (! $includeInactive) {
            $roomTypesQuery->where('is_active', true);
        }

        $roomTypes = $roomTypesQuery->get();
        $amenitiesMap = $this->roomTypeAmenitiesMap($roomTypes->pluck('id')->all());

        $data = $roomTypes->map(function (RoomType $roomType) use ($amenitiesMap) {
            return [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'code' => $roomType->code,
                'bed_type' => $roomType->bed_type,
                'bed_count' => $roomType->bed_count,
                'capacity' => $roomType->capacity,
                'price_per_night' => (float) $roomType->price_per_night,
                'description' => $roomType->description,
                'is_active' => (bool) $roomType->is_active,
                'amenities' => $amenitiesMap[$roomType->id] ?? [],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function rooms(Hotel $hotel, Request $request)
    {
        $query = Room::with('roomType')
            ->where('hotel_id', $hotel->id)
            ->orderBy('room_number');

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->integer('room_type_id'));
        }

        $rooms = $query->get();
        $amenitiesMap = $this->roomTypeAmenitiesMap($rooms->pluck('room_type_id')->unique()->values()->all());

        $data = $rooms->map(function (Room $room) use ($amenitiesMap) {
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'room_type' => [
                    'id' => $room->roomType?->id,
                    'name' => $room->roomType?->name,
                    'code' => $room->roomType?->code,
                    'bed_type' => $room->roomType?->bed_type,
                    'bed_count' => $room->roomType?->bed_count,
                    'capacity' => $room->roomType?->capacity,
                    'price_per_night' => (float) ($room->roomType?->price_per_night ?? 0),
                    'amenities' => $amenitiesMap[$room->room_type_id] ?? [],
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function roomDetails(Hotel $hotel, Room $room)
    {
        if ($room->hotel_id !== $hotel->id) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found for this hotel.',
            ], 404);
        }

        $room->load('roomType');
        $amenitiesMap = $this->roomTypeAmenitiesMap([$room->room_type_id]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'status' => $room->status,
                'room_type' => [
                    'id' => $room->roomType?->id,
                    'name' => $room->roomType?->name,
                    'code' => $room->roomType?->code,
                    'bed_type' => $room->roomType?->bed_type,
                    'bed_count' => $room->roomType?->bed_count,
                    'capacity' => $room->roomType?->capacity,
                    'price_per_night' => (float) ($room->roomType?->price_per_night ?? 0),
                    'description' => $room->roomType?->description,
                    'amenities' => $amenitiesMap[$room->room_type_id] ?? [],
                ],
            ],
        ]);
    }

    public function availabilityCheck(Hotel $hotel, Request $request)
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'room_type_id' => ['nullable', 'integer', 'exists:room_types,id'],
            'rooms_needed' => ['nullable', 'integer', 'min:1'],
        ]);

        if (! empty($validated['room_type_id'])) {
            $roomTypeBelongsToHotel = RoomType::where('id', $validated['room_type_id'])
                ->where('hotel_id', $hotel->id)
                ->exists();

            if (! $roomTypeBelongsToHotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid room type for this hotel.',
                ], 422);
            }
        }

        $availableRooms = $this->availableRoomsQuery(
            $hotel->id,
            $validated['check_in'],
            $validated['check_out'],
            $validated['room_type_id'] ?? null
        )->with('roomType')->get();

        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $roomsNeeded = (int) ($validated['rooms_needed'] ?? 1);

        return response()->json([
            'success' => true,
            'data' => [
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'nights' => max(1, $nights),
                'rooms_needed' => $roomsNeeded,
                'available_rooms_count' => $availableRooms->count(),
                'can_fulfill' => $availableRooms->count() >= $roomsNeeded,
                'rooms' => $availableRooms->map(function (Room $room) {
                    return [
                        'id' => $room->id,
                        'room_number' => $room->room_number,
                        'floor' => $room->floor,
                        'room_type_id' => $room->room_type_id,
                        'room_type_name' => $room->roomType?->name,
                        'price_per_night' => (float) ($room->roomType?->price_per_night ?? 0),
                    ];
                })->values(),
            ],
        ]);
    }

    public function createBooking(Hotel $hotel, Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'min:6', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'total_guests' => ['required', 'integer', 'min:1'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'room_ids' => ['required', 'array', 'min:1'],
            'room_ids.*' => ['integer', 'distinct', 'exists:rooms,id'],
        ]);

        $nights = max(
            1,
            Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']))
        );

        $booking = DB::transaction(function () use ($hotel, $validated, $nights) {
            $guest = Guest::firstOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'phone' => $validated['phone'],
                ],
                [
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'] ?? null,
                ]
            );

            if ($guest->full_name !== $validated['full_name'] || $guest->email !== ($validated['email'] ?? null)) {
                $guest->update([
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'] ?? null,
                ]);
            }

            $booking = Booking::create([
                'hotel_id' => $hotel->id,
                'guest_id' => $guest->id,
                'total_guests' => $validated['total_guests'],
                'status' => 'reserved',
            ]);

            foreach ($validated['room_ids'] as $roomId) {
                $room = Room::with('roomType')
                    ->where('hotel_id', $hotel->id)
                    ->find($roomId);

                if (! $room) {
                    throw ValidationException::withMessages([
                        'room_ids' => ["Room {$roomId} does not belong to this hotel."],
                    ]);
                }

                if (in_array($room->getRawOriginal('status'), ['dirty', 'cleaning', 'maintenance', 'out_of_order'], true)) {
                    throw ValidationException::withMessages([
                        'room_ids' => ["Room {$room->room_number} is currently unavailable."],
                    ]);
                }

                $hasConflict = BookingRoom::query()
                    ->where('room_id', $room->id)
                    ->where('check_in', '<', $validated['check_out'])
                    ->where('check_out', '>', $validated['check_in'])
                    ->whereHas('booking', function ($q) {
                        $q->whereNotIn('status', ['cancelled', 'no_show']);
                    })
                    ->exists();

                if ($hasConflict) {
                    throw ValidationException::withMessages([
                        'room_ids' => ["Room {$room->room_number} is not available for selected dates."],
                    ]);
                }

                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $room->id,
                    'price_per_night' => (float) ($room->roomType?->price_per_night ?? 0),
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                ]);
            }

            return $booking->fresh(['guest', 'bookingRooms.room.roomType']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data' => $this->formatBooking($booking, $nights),
        ], 201);
    }

    public function bookingConfirmation(Hotel $hotel, Booking $booking, Request $request)
    {
        if ($booking->hotel_id !== $hotel->id) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found for this hotel.',
            ], 404);
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Phone or email is required to view booking confirmation.',
            ], 422);
        }

        $booking->load(['guest', 'bookingRooms.room.roomType']);
        $guest = $booking->guest;

        $phoneMatches = ! empty($validated['phone']) && $guest && $guest->phone === $validated['phone'];
        $emailMatches = ! empty($validated['email']) && $guest && $guest->email === $validated['email'];

        if (! $phoneMatches && ! $emailMatches) {
            return response()->json([
                'success' => false,
                'message' => 'Guest verification failed.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatBooking($booking),
        ]);
    }

    public function cancelBooking(Hotel $hotel, Booking $booking, Request $request)
    {
        if ($booking->hotel_id !== $hotel->id) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found for this hotel.',
            ], 404);
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
        ]);

        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Phone or email is required to cancel booking.',
            ], 422);
        }

        $booking->load('guest');
        $guest = $booking->guest;
        $phoneMatches = ! empty($validated['phone']) && $guest && $guest->phone === $validated['phone'];
        $emailMatches = ! empty($validated['email']) && $guest && $guest->email === $validated['email'];

        if (! $phoneMatches && ! $emailMatches) {
            return response()->json([
                'success' => false,
                'message' => 'Guest verification failed.',
            ], 403);
        }

        if (in_array($booking->status, ['checked_out', 'cancelled'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled.',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);
        $booking->refresh();
        $booking->load(['guest', 'bookingRooms.room.roomType']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.',
            'data' => $this->formatBooking($booking),
        ]);
    }

    public function bookingLookup(Hotel $hotel, Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Phone or email is required to find bookings.',
            ], 422);
        }

        $query = Booking::with(['guest', 'bookingRooms.room.roomType'])
            ->where('hotel_id', $hotel->id)
            ->whereHas('guest', function ($q) use ($validated) {
                if (! empty($validated['phone']) && ! empty($validated['email'])) {
                    $q->where(function ($guestQuery) use ($validated) {
                        $guestQuery->where('phone', $validated['phone'])
                            ->orWhere('email', $validated['email']);
                    });
                } elseif (! empty($validated['phone'])) {
                    $q->where('phone', $validated['phone']);
                } else {
                    $q->where('email', $validated['email']);
                }
            })
            ->latest();

        $bookings = $query->paginate((int) ($validated['per_page'] ?? 10));

        return response()->json([
            'success' => true,
            'data' => $bookings->getCollection()->map(fn(Booking $booking) => $this->formatBooking($booking))->values(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    protected function availableRoomsQuery(int $hotelId, string $checkIn, string $checkOut, ?int $roomTypeId = null)
    {
        return Room::query()
            ->where('hotel_id', $hotelId)
            ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->whereNotIn('status', ['dirty', 'cleaning', 'maintenance', 'out_of_order'])
            ->whereDoesntHave('bookingRooms', function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn)
                    ->whereHas('booking', function ($b) {
                        $b->whereNotIn('status', ['cancelled', 'no_show']);
                    });
            });
    }

    protected function formatBooking(Booking $booking, ?int $nights = null): array
    {
        $booking->loadMissing(['guest', 'bookingRooms.room.roomType']);

        $totalAmount = 0.0;
        $checkIn = $booking->bookingRooms->min('check_in');
        $checkOut = $booking->bookingRooms->max('check_out');

        foreach ($booking->bookingRooms as $bookingRoom) {
            $roomCheckIn = Carbon::parse($bookingRoom->check_in);
            $roomCheckOut = Carbon::parse($bookingRoom->check_out);
            $roomNights = max(1, $roomCheckIn->diffInDays($roomCheckOut));
            $totalAmount += $roomNights * (float) $bookingRoom->price_per_night;
        }

        $computedNights = $nights;
        if (! $computedNights && $checkIn && $checkOut) {
            $computedNights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        }

        return [
            'id' => $booking->id,
            'booking_reference' => 'BK-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
            'status' => $booking->status,
            'check_in' => $checkIn ? Carbon::parse($checkIn)->toDateString() : null,
            'check_out' => $checkOut ? Carbon::parse($checkOut)->toDateString() : null,
            'nights' => (int) ($computedNights ?? 0),
            'total_guests' => (int) $booking->total_guests,
            'total_amount' => round($totalAmount, 2),
            'guest' => [
                'id' => $booking->guest?->id,
                'full_name' => $booking->guest?->full_name,
                'phone' => $booking->guest?->phone,
                'email' => $booking->guest?->email,
            ],
            'rooms' => $booking->bookingRooms->map(function (BookingRoom $bookingRoom) {
                return [
                    'room_id' => $bookingRoom->room_id,
                    'room_number' => $bookingRoom->room?->room_number,
                    'room_type_id' => $bookingRoom->room?->room_type_id,
                    'room_type_name' => $bookingRoom->room?->roomType?->name,
                    'price_per_night' => (float) $bookingRoom->price_per_night,
                    'check_in' => $bookingRoom->check_in?->toDateString(),
                    'check_out' => $bookingRoom->check_out?->toDateString(),
                ];
            })->values(),
            'created_at' => optional($booking->created_at)?->toDateTimeString(),
        ];
    }

    protected function roomTypeAmenitiesMap(array $roomTypeIds): array
    {
        if (empty($roomTypeIds)) {
            return [];
        }

        $rows = DB::table('amenity_room')
            ->join('rooms', 'rooms.id', '=', 'amenity_room.room_id')
            ->join('amenities', 'amenities.id', '=', 'amenity_room.amenity_id')
            ->whereIn('rooms.room_type_id', $roomTypeIds)
            ->select(
                'rooms.room_type_id',
                'amenities.id as amenity_id',
                'amenities.name as amenity_name',
                'amenities.icon as amenity_icon'
            )
            ->distinct()
            ->orderBy('amenities.name')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->room_type_id][] = [
                'id' => (int) $row->amenity_id,
                'name' => $row->amenity_name,
                'icon' => $row->amenity_icon,
            ];
        }

        return $map;
    }
}
