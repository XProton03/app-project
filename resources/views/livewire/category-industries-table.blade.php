<div wire:poll.5s>
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
                    {{-- <button wire:click="loadData">Refresh Data</button> --}}
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categoryIndustries as $index => $category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $category->category }}</td>
                                    <td>{{ $category->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
