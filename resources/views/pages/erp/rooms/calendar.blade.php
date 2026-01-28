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
<!-- FullCalendar core CSS -->
<!-- FullCalendar Scheduler (includes resourceTimeline) CSS -->
<script src="/assets/plugins/fullcalendar-scheduler/index.global.min.js"></script>

@php
    $localSchedulerCss = public_path('assets/plugins/fullcalendar-scheduler/index.global.min.css');
    $localOldCss = public_path('assets/plugins/fullcalendar/fullcalendar.min.css');
@endphp

@if (file_exists($localSchedulerCss))
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar-scheduler/index.global.min.css') }}">
@elseif (file_exists($localOldCss))
    <link rel="stylesheet" href="{{ asset('assets/plugins/fullcalendar/fullcalendar.min.css') }}">
@else
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.css">
@endif
@endsection

@section('js')
<script>
function loadScript(url){
    return new Promise(function(resolve, reject){
        var s = document.createElement('script');
        s.src = url;
        s.async = true;
        s.onload = function(){ resolve(url); };
        s.onerror = function(e){ reject(e); };
        document.head.appendChild(s);
    });
}

async function tryLoad(urls){
    for(const u of urls){
        try{
            await loadScript(u);
            console.info('Loaded', u);
            return true;
        }catch(e){
            console.warn('Failed loading', u, e);
        }
    }
    return false;
}

document.addEventListener('DOMContentLoaded', async function () {
    const calendarEl = document.getElementById('room-calendar');
    const fallbackEl = document.getElementById('room-calendar-fallback');

    // try scheduler bundle on popular CDNs
    const cdnUrls = [
        'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js',
        'https://unpkg.com/fullcalendar-scheduler@6.1.8/index.global.min.js'
    ];
    let ok = await tryLoad(cdnUrls);
    if (!ok) {
        console.warn('CDN scheduler not available, trying local FullCalendar files...');
        const localScripts = [
            '{{ asset("assets/plugins/fullcalendar/fullcalendar.min.js") }}',
            '{{ asset("assets/plugins/fullcalendar/jquery.fullcalendar.js") }}'
        ];
        ok = await tryLoad(localScripts);
        if (!ok) {
    const localScheduler = "{{ file_exists(public_path('assets/plugins/fullcalendar-scheduler/index.global.min.js')) ? asset('assets/plugins/fullcalendar-scheduler/index.global.min.js') : '' }}";
    const localOld = "{{ file_exists(public_path('assets/plugins/fullcalendar/fullcalendar.min.js')) ? asset('assets/plugins/fullcalendar/fullcalendar.min.js') : '' }}";

    let ok = false;

    if (localScheduler) {
        ok = await tryLoad([localScheduler]);
    }

    if (!ok) {
        const cdnUrls = [
            'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.8/index.global.min.js',
            'https://unpkg.com/fullcalendar-scheduler@6.1.8/index.global.min.js'
        ];
        ok = await tryLoad(cdnUrls);
    }

    if (!ok && localOld) {
        // attempt to load older local fullcalendar (jQuery plugin)
        ok = await tryLoad([localOld, '{{ asset("assets/plugins/fullcalendar/jquery.fullcalendar.js") }}']);
    }

    if (!ok) {
        console.error('Failed to load FullCalendar from local files and CDNs. Falling back to server-rendered grid.');
        return;
    }

    // hide fallback grid when any calendar library loads
    if (fallbackEl) fallbackEl.style.display = 'none';
            console.error('Failed to load FullCalendar from CDNs and local files. Falling back to server-rendered grid.');
            // leave fallback visible
            return;
        }
    }

    // hide fallback grid when any calendar library loads
    if (fallbackEl) fallbackEl.style.display = 'none';

    // prefer resourceTimeline if plugin available
    const hasResourceTimeline = !!FullCalendar.resourceTimelinePlugin || (FullCalendar && FullCalendar.ResourceTimeline);

    const commonOptions = {
        // scheduler license (open-source / CC non-commercial attribution)
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: hasResourceTimeline ? 'resourceTimelineWeek,resourceTimelineMonth' : 'dayGridMonth,timeGridWeek'
        },
        eventClick: function(info) {
            // booking id is event.id formatted as bookingId-roomId in controller
            const bookingId = (info.event.id || '').toString().split('-')[0];
            if (bookingId) {
                window.open('/booking/' + bookingId, '_blank');
            }
        },
        events: '{{ route("room.calendar.api") }}'
    };

    let calendar;

    try {
        if (hasResourceTimeline) {
            calendar = new FullCalendar.Calendar(calendarEl, Object.assign({}, commonOptions, {
                plugins: [ FullCalendar.resourceTimelinePlugin ],
                initialView: 'resourceTimelineWeek',
                resourceAreaHeaderContent: 'Rooms',
                resourceAreaWidth: '180px',
                resources: { url: '{{ route("room.calendar.resources") }}' }
            }));
        } else {
            // fallback to regular month view with room info in event titles
            calendar = new FullCalendar.Calendar(calendarEl, Object.assign({}, commonOptions, {
                initialView: 'dayGridMonth'
            }));
        }

        // Enhance events with popovers/tooltips showing quick actions/details
        calendar.render();

        // Initialize popovers for events after render (for resourceTimeline plugin)
        calendar.on('eventDidMount', function(info){
            try{
                // build popover HTML
                const props = info.event.extendedProps || {};
                const bookingId = props.booking_id || (info.event.id||'').toString().split('-')[0];
                const guest = props.guest_name || '';
                const status = props.status || '';
                const checkIn = props.check_in || '';
                const checkOut = props.check_out || '';

                const content = `
                    <div style="font-size:13px">
                        <div><strong>Guest:</strong> ${guest}</div>
                        <div><strong>Status:</strong> ${status}</div>
                        <div><strong>Check-in:</strong> ${checkIn}</div>
                        <div><strong>Check-out:</strong> ${checkOut}</div>
                        <div class="mt-2">
                            <a class="btn btn-sm btn-primary me-1" href="/booking/${bookingId}" target="_blank">View</a>
                        </div>
                    </div>
                `;

                // attach bootstrap popover
                const pop = new bootstrap.Popover(info.el, {
                    title: info.event.title,
                    content: content,
                    html: true,
                    trigger: 'hover focus',
                    placement: 'top',
                    container: 'body'
                });
            }catch(e){
                console.warn('eventDidMount popover error', e);
            }
        });
    } catch (e) {
        console.error('FullCalendar initialization failed', e);
        // leave a simple message in the calendar container
        if (calendarEl) {
            calendarEl.innerHTML = '<div class="alert alert-danger">Interactive calendar failed to initialize. Please check console for details.</div>';
        }
    }
});
</script>
@endsection
