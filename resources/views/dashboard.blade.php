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
                @livewire('dashboard')
            </div>
            <div class="row">
                @livewire('category-industries-table')
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

                                    calendar.render(); // Render the calendar

                                    // Fetch latest schedule data
                                    fetch('/api/schedules')
                                        .then(response => {
                                            console.log("Response status:", response.status); // Log response status
                                            return response.json();
                                        })
                                        .then(data => {
                                            if (Array.isArray(data) && data.length > 0) {
                                                data.forEach(event => {
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
