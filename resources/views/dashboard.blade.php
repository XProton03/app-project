@extends('layouts.app')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Home</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Prospect Leads</h6>
                            <h4 class="mb-3">{{ $prospectCount }}
                                {{-- <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i>
                            59.3%
                        </span> --}}
                            </h4>
                            {{-- <p class="mb-0 text-muted text-sm">You made an extra <span
                                class="text-primary">{{ $prospectCount }}</span>
                            this year
                        </p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Fresh Leads</h6>
                            <h4 class="mb-3">{{ $statusPendingFollowup }}
                                {{-- <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i>
                            59.3%
                        </span> --}}
                            </h4>
                            {{-- <p class="mb-0 text-muted text-sm">You made an extra <span
                                class="text-primary">{{ $prospectCount }}</span>
                            this year
                        </p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Followup</h6>
                            <h4 class="mb-3">{{ $statusFollowup }}
                                {{-- <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i>
                            59.3%
                        </span> --}}
                            </h4>
                            {{-- <p class="mb-0 text-muted text-sm">You made an extra <span
                                class="text-primary">{{ $prospectCount }}</span>
                            this year
                        </p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-danger">Total Rejected</h6>
                            <h4 class="mb-3 text-danger">{{ $statusFailed }}
                                {{-- <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i>
                            59.3%
                        </span> --}}
                            </h4>
                            {{-- <p class="mb-0 text-muted text-sm">You made an extra <span
                                class="text-primary">{{ $prospectCount }}</span>
                            this year
                        </p> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-success">Total Closed</h6>
                            <h4 class="mb-3 text-success">{{ $statusClosed }}
                                {{-- <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i>
                            59.3%
                        </span> --}}
                            </h4>
                            {{-- <p class="mb-0 text-muted text-sm">You made an extra <span
                                class="text-primary">{{ $prospectCount }}</span>
                            this year
                        </p> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="ti ti-table"></i> Status Leads</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($statusLeads as $status)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $status->status }}</td>
                                                <td>{{ $status->total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="col-md-6">
                                <canvas id="statusLeadsChart"></canvas>
                            </div>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> 
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var ctx = document.getElementById('statusLeadsChart').getContext('2d');
                                    console.log("Status Counts Data:",
                                        @json($statusCounts)); // Debugging log to check the data structure
                                    var statusCounts = @json($statusCounts); // Pass the statusCounts data to JavaScript

                                    var labels = statusCounts.map(function(item) {
                                        return item.status; // Change 'name' to 'status' to match the correct field
                                    });

                                    var data = statusCounts.map(function(item) {
                                        return item.count; // Ensure this is correctly referencing the count
                                    });

                                    var chart = new Chart(ctx, {
                                        type: 'bar', // Change back to 'bar'
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                label: 'Jumlah Leads',
                                                data: data,
                                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            plugins: {
                                                datalabels: {
                                                    anchor: 'end',
                                                    align: 'end',
                                                    formatter: (context) => {
                                                        return context.dataset.data[context
                                                            .dataIndex]; // Display the count on the bar
                                                    }
                                                }
                                            },

                                            indexAxis: 'y', // Set indexAxis to 'y' for horizontal bars
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    });
                                });
                            </script> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="ti ti-dots"></i> Category Industries</h5>
                        </div>
                        <div class="card-body">
                            @livewire('category-industries-table')
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="ti ti-calendar"></i> Visit Schedule</h5>
                        </div>
                        <div class="card-body">
                            <div id='calendar'></div> <!-- Placeholder for the calendar -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var calendarEl = document.getElementById('calendar');
                                    if (!calendarEl) {
                                        console.error("Calendar element not found"); // Error log
                                        return;
                                    }
                                    var calendar = new FullCalendar.Calendar(calendarEl, {
                                        initialView: 'dayGridMonth',
                                        events: [] // Temporarily set to an empty array to test rendering
                                    });

                                    console.log("Initial calendar data loaded"); // Debugging log

                                    calendar.render(); // Render the calendar

                                    // Fetch latest schedule data
                                    fetch('/api/schedules')
                                        .then(response => {
                                            console.log("Response status:", response.status); // Log response status
                                            return response.json();
                                        })
                                        .then(data => {
                                            console.log("Fetched new schedule data:", data); // Debugging log
                                            // Log the structure of the fetched data
                                            console.log("Data structure:", JSON.stringify(data, null, 2)); // Log the data structure

                                            if (Array.isArray(data) && data.length > 0) {
                                                data.forEach(event => {
                                                    console.log("Adding event:", event); // Log each event being added
                                                    calendar.addEvent({
                                                        title: event.company_name, // Use company_name as the title
                                                        start: event.schedule, // Use schedule for the start date
                                                        color: event.status_leads_id == 1 ? 'primary' : 'green',
                                                        extendedProps: { // Simpan data tambahan di extendedProps
                                                            followup_by: event.followup_by || "Tidak ada deskripsi",
                                                        }
                                                    });
                                                });
                                            } else {
                                                console.warn("No new events to update or data format is incorrect:",
                                                    data); // Warning log
                                            }
                                        })
                                        .catch(error => console.error("Error fetching schedule data:", error)); // Error log

                                    // Add click event listener for calendar events
                                    calendar.on('eventClick', function(info) {
                                        alert('---Jadwal Visit---\n\n🏢 Kantor: ' + info.event.title + '\n📆 Tanggal: ' + info
                                            .event
                                            .start
                                            .toLocaleDateString(
                                                'id-ID', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric'
                                                }) +
                                            '\n👤 Engineer: ' + info.event.extendedProps.followup_by);
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
