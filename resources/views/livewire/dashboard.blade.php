<div wire:poll.5s>
    <div class="row">
        {{-- <button wire:click="loadData">Refresh Data</button> --}}
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
                    <h4 class="mb-3">{{ $statusFreshLead }}
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
</div>
