@extends('layout.erp.app')

@section('content')

        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12 d-flex widget-path widget-service">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-userhead">
                                <div class="home-usercount">
                                    <span><img src="{{ asset('assets') }}/img/icons/calendar.svg" alt="New Booking"></span>
                                    <h6>New Booking</h6>
                                </div>
                                <div class="home-useraction">
                                    <a class="delete-table bg-white" href="javascript:void(0);" data-bs-toggle="dropdown"
                                        aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>

                                </div>
                            </div>
                            <div class="home-usercontent">
                                <div class="home-usercontents">
                                    <div class="home-usercontentcount">
                                        <img src="{{ asset('assets') }}/img/icons/arrow-up.svg" alt="img"
                                            class="me-2">
                                        <span id="newBookingsCount" class="counters">{{ $newBookingsCount ?? 0 }}</span>
                                    </div>
                                    <h5> This Month</h5>
                                </div>
                                <div class="homegraph">
                                    <img src="{{ asset('assets') }}/img/graph/graph1.png" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex widget-path widget-service">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user home-provider">
                            <div class="home-userhead">
                                <div class="home-usercount">
                                    <span><img src="{{ asset('assets') }}/img/icons/service-icon-01.svg" alt="Available Rooms"></span>
                                    <h6>Available Rooms</h6>
                                </div>
                                <div class="home-useraction">
                                    <a class="delete-table bg-white" href="javascript:void(0);" data-bs-toggle="dropdown"
                                        aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                        <li>
                                            <a href="providers.html" class="dropdown-item"> View</a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item"> Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="home-usercontent">
                                <div class="home-usercontents">
                                    <div class="home-usercontentcount">
                                        <img src="{{ asset('assets') }}/img/icons/arrow-up.svg" alt="img"
                                            class="me-2">
                                        <span id="availableRoomsCount" class="counters">{{ $availableRooms ?? 0 }}</span>
                                    </div>
                                    <h5> Today</h5>
                                </div>
                                <div class="homegraph">
                                    <img src="{{ asset('assets') }}/img/graph/graph2.png" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex widget-path widget-service">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user home-service">
                            <div class="home-userhead">
                                <div class="home-usercount">
                                    <span><img src="{{ asset('assets') }}/img/icons/check-icon.svg" alt="Checkout"></span>
                                    <h6>Checkout</h6>
                                </div>
                                <div class="home-useraction">
                                    <a class="delete-table bg-white" href="javascript:void(0);" data-bs-toggle="dropdown"
                                        aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                        <li>
                                            <a href="services.html" class="dropdown-item"> View</a>
                                        </li>
                                        <li>
                                            <a href="edit-service.html" class="dropdown-item"> Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="home-usercontent">
                                <div class="home-usercontents">
                                    <div class="home-usercontentcount">
                                        <img src="{{ asset('assets') }}/img/icons/arrow-up.svg" alt="img"
                                            class="me-2">
                                        <span id="checkoutsToday" class="counters">{{ $checkoutsToday ?? 0 }}</span>
                                    </div>
                                    <h5> Today</h5>
                                </div>
                                <div class="homegraph">
                                    <img src="{{ asset('assets') }}/img/graph/graph3.png" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12 d-flex widget-path widget-service">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user home-subscription">
                            <div class="home-userhead">
                                <div class="home-usercount">
                                    <span><img src="{{ asset('assets') }}/img/icons/money.svg" alt="img"></span>
                                    <h6>Revenue</h6>
                                </div>
                                <div class="home-useraction">
                                    <a class="delete-table bg-white" href="javascript:void(0);" data-bs-toggle="dropdown"
                                        aria-expanded="true">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                        <li>
                                            <a href="membership.html" class="dropdown-item"> View</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item"> Edit</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="home-usercontent">
                                <div class="home-usercontents">
                                    <div class="home-usercontentcount">
                                        <img src="{{ asset('assets') }}/img/icons/arrow-up.svg" alt="img"
                                            class="me-2">
                                        <span class="counters" data-count="650">$650</span>
                                    </div>
                                    <h5> Current Month</h5>
                                </div>
                                <div class="homegraph">
                                    <img src="{{ asset('assets') }}/img/graph/graph4.png" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-6 col-12 d-flex  widget-path">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-head-user">
                                <h2>Revenue</h2>
                                <div class="home-select">
                                    <div class="dropdown">
                                        <button class="btn btn-action btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Monthly
                                        </button>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="dropdown">
                                        <a class="delete-table bg-white" href="javascript:void(0);"
                                            data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> View</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="chartgraph">
                                <div id="chart-view"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-12 d-flex  widget-path">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-head-user">
                                <h2>Booking Summary</h2>
                                <div class="home-select">
                                    <div class="dropdown">
                                        <button class="btn btn-action btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Monthly
                                        </button>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="dropdown">
                                        <a class="delete-table bg-white" href="javascript:void(0);"
                                            data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> View</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="chartgraph">
                                <div id="chart-booking" data-remote="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-sm-12 d-flex widget-path">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-head-user home-graph-header">
                                <h2>Top Countries</h2>
                                <div class="home-select">
                                    <div class="dropdown">
                                        <button class="btn btn-action btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Monthly
                                        </button>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="dropdown">
                                        <a class="delete-table bg-white" href="javascript:void(0);"
                                            data-bs-toggle="dropdown" aria-expanded="true">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> View</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"> Edit</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="chartgraph">
                                <div class="row align-items-center">
                                    <div class="col-lg-7">
                                        <div id="world_map" style="height: 150px"></div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="bookingmap">
                                            <ul>
                                                <li>
                                                    <span><img src="{{ asset('assets') }}/img/flags/us.png"
                                                            alt="img" class="me-2">United State</span>
                                                    <h6>60%</h6>
                                                </li>
                                                <li>
                                                    <span><img src="{{ asset('assets') }}/img/flags/in.png"
                                                            alt="img" class="me-2">India</span>
                                                    <h6>80%</h6>
                                                </li>
                                                <li>
                                                    <span><img src="{{ asset('assets') }}/img/flags/ca.png"
                                                            alt="img" class="me-2">Canada</span>
                                                    <h6>50%</h6>
                                                </li>
                                                <li>
                                                    <span><img src="{{ asset('assets') }}/img/flags/au.png"
                                                            alt="img" class="me-2">Australia</span>
                                                    <h6>75%</h6>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12 d-flex widget-path">
                <div class="card">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-head-user home-graph-header">
                                <h2>Booking Statistics</h2>
                                <a href="{{ url('booking') }}" class="btn btn-viewall">View All<img
                                        src="{{ asset('assets') }}/img/icons/arrow-right.svg" class="ms-2"
                                        alt="img"></a>
                            </div>
                            <div class="chartgraph">
                                <div class="row align-items-center">
                                    <div class="col-lg-7 col-sm-6">
                                        <div id="chart-bar" data-remote="true"></div>
                                    </div>
                                    <div class="col-lg-5 col-sm-6">
                                        <div class="bookingstatus">
                                            <ul>
                                                <li>
                                                    <span></span>
                                                    <h6>Completed</h6>
                                                </li>
                                                <li class="process-status">
                                                    <span></span>
                                                    <h6>Process</h6>
                                                </li>
                                                <li class="process-pending">
                                                    <span></span>
                                                    <h6>Pending</h6>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 widget-path">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="home-user">
                            <div class="home-head-user home-graph-header">
                                <h2>Recent Booking</h2>
                                <a href="{{ url('booking') }}" class="btn btn-viewall">View All<img
                                        src="{{ asset('assets') }}/img/icons/arrow-right.svg" class="ms-2"
                                        alt="img"></a>
                            </div>
                            <div class="table-responsive datatable-nofooter">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Booking Time</th>
                                            <th>Guest</th>
                                            <th>Phone</th>
                                            <th>Room</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                                @forelse($recentBookings as $idx => $booking)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ optional($booking->created_at)->format('d M Y') }}</td>
                                                <td>
                                                    @php
                                                        $br = $booking->bookingRooms->first();
                                                    @endphp
                                                    @if($br)
                                                        {{ optional($br->check_in)->format('d M Y') }} - {{ optional($br->check_out)->format('d M Y') }}
                                                    @else
                                                        {{ optional($booking->check_in)->format('d M Y') }} - {{ optional($booking->check_out)->format('d M Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="table-profileimage">
                                                        {{-- <img src="{{ asset('assets') }}/img/customer/user-01.jpg" class="me-2" alt="img"> --}}
                                                        <span>{{ $booking->guest?->full_name ?? 'Guest' }}</span>
                                                    </a>
                                                </td>
                                                <td>{{ $booking->guest?->phone ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $service = $booking->bookingRooms->first()?->room?->roomType?->name ?? 'Room';
                                                    @endphp
                                                    <a href="#" class="table-imgname">
                                                        <span>{{ $service }}</span>
                                                    </a>
                                                </td>
                                                <td>${{ number_format($booking->total_amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = $booking->status === 'cancelled' ? 'delete' : ($booking->status === 'checked_in' ? 'active' : ($booking->status === 'checked_out' ? 'success' : 'pending'));
                                                        $statusLabel = ucwords(str_replace('_', ' ', $booking->status));
                                                    @endphp
                                                    <h6 class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</h6>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No recent bookings found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
(function(){
    // create occupancy radial chart if element exists
    let occupancyRadialChart = null;
    function createRadial(v){
        const el = document.querySelector('#occupancy-radial');
        if (!el) return;
        if (occupancyRadialChart) {
            occupancyRadialChart.updateSeries([v]);
            return;
        }
        occupancyRadialChart = new ApexCharts(el, {
            chart: { type: 'radial', height: 80 },
            series: [v],
            labels: ['Occupancy'],
            plotOptions: { radialBar: { hollow: { size: '60%' } } }
        });
        occupancyRadialChart.render();
    }

    async function pollOccupancy(){
        try{
            const res = await fetch('{{ url('api/occupancy/summary') }}', { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('totalRoomsCount')?.innerText = data.totalRooms ?? 0;
            document.getElementById('occupiedRoomsCount')?.innerText = data.occupied ?? 0;
            document.getElementById('availableRoomsCount')?.innerText = data.available ?? 0;
            document.getElementById('occupancyRateSmall')?.innerText = (data.occupancyRate ?? 0) + '%';
            createRadial(data.occupancyRate ?? 0);
        }catch(err){ console.error('occupancy poll failed', err); }
    }

    // initial
    document.addEventListener('DOMContentLoaded', function(){
        pollOccupancy();
        setInterval(pollOccupancy, 30000);
    });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // initialize chart instances (so they can be updated by polling)
    let bookingBarChart = null;
    let bookingPieChart = null;

    function renderBookingBar(labels, series){
        const el = document.querySelector('#chart-booking');
        if (!el) return;
        const options = {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            series: [{ name: 'Bookings', data: series }],
            plotOptions: { bar: { horizontal: false, columnWidth: '60%', endingShape: 'rounded' } },
            dataLabels: { enabled: false },
            xaxis: { categories: labels },
            tooltip: { y: { formatter: val => val + ' bookings' } }
        };
        el.innerHTML = '';
        if (bookingBarChart) {
            bookingBarChart.updateOptions({ xaxis: { categories: labels } });
            bookingBarChart.updateSeries([{ name: 'Bookings', data: series }]);
        } else {
            bookingBarChart = new ApexCharts(el, options);
            bookingBarChart.render();
        }
    }

    function renderBookingPie(stats){
        const el = document.querySelector('#chart-bar');
        if (!el) return;
        const series = [stats.completed, stats.process, stats.pending];
        const total = series.reduce((a,b)=>a+b,0);
        const options = {
            series: series,
            chart: { width: 200, type: 'pie' },
            labels: ['Completed', 'Process', 'Pending'],
            colors: ['#1BA345','#0081FF','#FEC001'],
            tooltip: { y: { formatter: function(val){ const pct = total ? ((val / total) * 100).toFixed(1) : 0; return val + ' (' + pct + '%)'; } } },
            legend: { position: 'right' },
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
        };
        el.innerHTML = '';
        if (bookingPieChart) {
            bookingPieChart.updateSeries(series);
            bookingPieChart.updateOptions({ labels: ['Completed','Process','Pending'] });
        } else {
            bookingPieChart = new ApexCharts(el, options);
            bookingPieChart.render();
        }
    }

    // initial render from server-side values if present
    renderBookingBar(@json($labels ?? []), @json($series ?? []));
    renderBookingPie(@json($bookingStats ?? ['completed'=>0,'process'=>0,'pending'=>0]));

    async function fetchBookingStats(){
        try{
            const res = await fetch('{{ url('api/bookings/stats') }}', { credentials: 'same-origin' });
            if (!res.ok) return;
            const data = await res.json();
            renderBookingBar(data.labels || [], data.series || []);
            renderBookingPie(data.bookingStats || { completed: 0, process: 0, pending: 0 });
        }catch(err){ console.error('booking stats fetch failed', err); }
    }

    // poll for updates every 60s
    fetchBookingStats();
    setInterval(fetchBookingStats, 60000);

});
</script>
@endsection
