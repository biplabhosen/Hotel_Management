@extends('layout.erp.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Room Calendar</h3>
    <div id="room-calendar"></div>

    {{-- Server-rendered fallback grid (visible if JS fails to init scheduler) --}}
    <div id="room-calendar-fallback">
        <div class="alert alert-info">Interactive calendar unavailable — showing simple room-by-day grid.</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Room</th>
                        @foreach (\Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth) as $d)
                            <th class="text-center" style="min-width:48px">{{ $d->format('d') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendar as $room)
                        <tr>
                            <td><strong>{{ $room['room_number'] }}</strong><br><small class="text-muted">{{ $room['room_type'] }}</small></td>
                            @foreach (\Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth) as $d)
                                @php
                                    $ds = $d->toDateString();
                                    $occ = $room['occupancy'][$ds] ?? null;
                                @endphp
                                <td class="text-center p-1" style="vertical-align:middle">
                                    @if($occ)
                                        <div style="font-size:11px;">
                                            <div><strong>#{{ $occ['booking_id'] }}</strong></div>
                                            <div class="text-truncate" style="max-width:80px">{{ $occ['guest_name'] ?? '' }}</div>
                                        </div>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('css')
<!-- FullCalendar Scheduler (v6 index.global) CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.css">
@endsection

@section('js')
<!-- FullCalendar Scheduler (v6 index.global) JS; do not mix with local/npm builds -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const calendarEl = document.getElementById('room-calendar');
    const fallbackEl = document.getElementById('room-calendar-fallback');

    // Console error inspection: fail fast if the scheduler bundle isn't loaded.
    if (!window.FullCalendar) {
        console.error('Scheduler not loaded: FullCalendar global is missing. Check the CDN script tag.');
        return; // keep fallback visible
    }

    // FullCalendar version consistency check (must be v6.x).
    if (FullCalendar.version && !FullCalendar.version.startsWith('6.')) {
        console.warn('FullCalendar version mismatch:', FullCalendar.version);
    }

    // FullCalendar.ResourceTimeline availability check (resource + resourceTimeline plugins are required).
    const resourceTimelineExport = FullCalendar.ResourceTimeline || FullCalendar.resourceTimelinePlugin;
    const hasGlobalResourceTimeline = Array.isArray(FullCalendar.globalPlugins)
        && FullCalendar.globalPlugins.some(p => p && p.name === '@fullcalendar/resource-timeline');
    if (!resourceTimelineExport && !hasGlobalResourceTimeline) {
        console.error('Scheduler not loaded: resourceTimeline plugin missing. Use fullcalendar-scheduler v6 index.global build.');
        return; // keep fallback visible
    }

    // Network tab verification: confirm calendar resources/events endpoints return 200 with JSON.
    const roomsUrl = '{{ route("room.calendar.resources") }}';
    const bookingsUrl = '{{ route("room.calendar.api") }}';
    console.info('Fetching', roomsUrl, 'and', bookingsUrl, '; verify status/response in Network tab.');

    const fetchJson = async (url) => {
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) {
            console.error('API error', url, res.status, res.statusText);
            throw new Error('API error: ' + url);
        }
        return res.json();
    };

    // Normalize resources to FullCalendar format: { id, title, ... }.
    const mapRoom = (room) => {
        // Accept both Room models and already-normalized resource objects.
        const id = room.id ?? room.room_id ?? room.roomId ?? room.number;
        const number = room.room_number ?? room.number ?? room.code ?? id;
        const type = room.room_type ?? room.type ?? room.roomType ?? '';
        const title = room.title ?? room.name ?? (type ? `${number} - ${type}` : String(number));
        return {
            ...room,
            id: id != null ? String(id) : null,
            title: title != null ? String(title) : null,
            room_number: room.room_number ?? number,
            room_type: room.room_type ?? type
        };
    };

    // Normalize bookings to FullCalendar events with resourceId mapping.
    const mapBooking = (booking) => {
        // Accept both Booking models and already-normalized event objects.
        const id = booking.id ?? booking.booking_id ?? booking.bookingId;
        const roomId = booking.resourceId ?? booking.resource_id ?? booking.room_id ?? booking.roomId ?? booking.room?.id ?? booking.room?.room_id;
        const start = booking.start ?? booking.start_date ?? booking.startDate ?? booking.check_in;
        const end = booking.end ?? booking.end_date ?? booking.endDate ?? booking.check_out;
        const guest = booking.guest_name ?? booking.guest ?? booking.customer_name ?? '';
        const status = booking.status ?? booking.extendedProps?.status ?? '';
        const title = booking.title ?? (guest ? guest : (id ? `Booking #${id}` : 'Booking'));
        return {
            ...booking,
            id: id != null ? String(id) : null,
            title,
            start,
            end,
            allDay: booking.allDay ?? true,
            resourceId: roomId != null ? String(roomId) : null,
            extendedProps: {
                ...(booking.extendedProps || {}),
                booking_id: booking.extendedProps?.booking_id ?? id,
                guest_name: booking.extendedProps?.guest_name ?? guest,
                status,
                check_in: booking.extendedProps?.check_in ?? start,
                check_out: booking.extendedProps?.check_out ?? end
            }
        };
    };

    let rooms = [];
    let bookings = [];

    try {
        const roomPayload = await fetchJson(roomsUrl);
        const bookingPayload = await fetchJson(bookingsUrl);

        rooms = (Array.isArray(roomPayload) ? roomPayload : (roomPayload.data ?? roomPayload.rooms ?? []))
            .map(mapRoom)
            .filter(r => r.id && r.title);

        bookings = (Array.isArray(bookingPayload) ? bookingPayload : (bookingPayload.data ?? bookingPayload.bookings ?? []))
            .map(mapBooking)
            .filter(e => e.id && e.start && e.end && e.resourceId);
    } catch (e) {
        console.error('Failed to load API data', e);
        return; // keep fallback visible
    }

    // Hide fallback only after data loads and scheduler is ready.
    if (fallbackEl) fallbackEl.style.display = 'none';

    const calendarOptions = {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimelineDay',
        height: 'auto',
        resourceAreaHeaderContent: 'Rooms',
        resourceAreaWidth: '220px',
        resources: rooms,
        events: bookings,
        editable: false,
        selectable: false,
        eventStartEditable: false,
        eventDurationEditable: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        eventClick: function (info) {
            // Read-only: allow quick view without edits.
            const bookingId = info.event.extendedProps.booking_id || info.event.id;
            if (bookingId) {
                window.open('/booking/' + bookingId, '_blank');
            }
        }
    };

    // For global bundle, plugins are pre-registered; only attach if exported (non-global builds).
    if (resourceTimelineExport) {
        calendarOptions.plugins = [ resourceTimelineExport ];
    }

    const calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);

    calendar.render();

    // calendar.getResources() sanity test (requires resource plugin).
    if (typeof calendar.getResources === 'function') {
        console.info('Resources loaded:', calendar.getResources().length);
    } else {
        console.warn('calendar.getResources() is unavailable; scheduler plugin may not be loaded.');
    }
});
</script>
@endsection

