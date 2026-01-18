@extends('layout.erp.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Room Calendar</h3>
    <div id="room-calendar"></div>
</div>
@endsection

@section('js')
<!-- FullCalendar CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">

<!-- FullCalendar JS (GLOBAL build – IMPORTANT) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Bootstrap (for tooltip, optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar NOT loaded');
        return;
    }

    let calendarEl = document.getElementById('room-calendar');

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },

        events: '{{ route("room.calendar.api") }}',

        eventClick: function(info) {
            let bookingId = info.event.id.split('-')[0];
            window.open('/bookings/' + bookingId, '_blank');
        },

        eventDidMount: function(info) {
            let content =
                info.event.title +
                '\nStatus: ' + info.event.extendedProps.status +
                '\nRoom: ' + info.event.extendedProps.room;

            new bootstrap.Tooltip(info.el, {
                title: content,
                placement: 'top',
                container: 'body'
            });
        }
    });

    calendar.render();
});
</script>
@endsection
