<div class="row">
    <div class="col-md-12 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-table"></i> Leads Status Graph</h5>
            </div>
            <div class="card-body">
                <div style="width: 100%; height: 350px">
                    <canvas id="statusLeadsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="ti ti-dots"></i> Industries Category</h5>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let ctx = document.getElementById('statusLeadsChart').getContext('2d');

        let maxData = Math.max(...@json($data)); // Ambil nilai tertinggi untuk skala X

        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Total Leads',
                    data: @json($data),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 30 // Tambahkan ruang di kanan agar label tidak terpotong
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end', // Posisi label di ujung bar
                        align: 'right', // Label berada di ujung bar ke kanan
                        color: 'black', // Warna teks agar mudah terbaca
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return value
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: maxData * 2, // Tambahkan 10% dari nilai tertinggi agar tidak terpotong
                        ticks: {
                            precision: 0
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Update chart saat data berubah
        Livewire.on('refreshChart', data => {
            let maxData = Math.max(...data.data); // Update nilai maksimum
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.data;
            chart.options.scales.x.max = maxData * 2; // Update batas maksimum
            chart.update();
        });
    });
</script>
