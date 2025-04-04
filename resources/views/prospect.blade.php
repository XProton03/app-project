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
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Activities</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="ti ti-database"></i> Data prospect</h5>
                    </div>
                    <div class="card-body">
                        <table id="leadsTable" class="table dt-responsive table-striped" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Updated At</th>
                                    <th>Last Followup</th>
                                    <th>Followup by</th>
                                    <th>State</th>
                                    <th>Company</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated here by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        let table = $('#leadsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/api/prospect-data') }}",
            },
            columns: [{
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: true
                },
                {
                    data: 'last_followup',
                    name: 'last_followup'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'status_name',
                    name: 'status_name'
                },
                {
                    data: 'company_name',
                    name: 'company_name',
                    render: function(data, type, row) {
                        return data ? data.toUpperCase() : ''; // Ubah ke huruf kapital
                    }
                },
                {
                    data: 'notes',
                    name: 'notes'
                }

            ],
            order: [
                [0, 'desc']
            ],

        });

        // Update otomatis setiap 30 detik
        // setInterval(function() {
        //     table.ajax.reload(null, false);
        // }, 30000);
    });
</script>
@endsection