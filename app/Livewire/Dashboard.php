<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProspectLead;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $prospectCount = [];
    public $statusFailed = [];
    public $statusFreshLead = [];
    public $statusFollowup = [];
    public $statusClosed = [];

    public function loadData()
    {
        $this->prospectCount = ProspectLead::all()->count();
        $this->statusFailed = ProspectLead::where('status_leads_id', '10')->count();
        $this->statusFreshLead = ProspectLead::where('status_leads_id', 1)->count();
        $this->statusFollowup = ProspectLead::where('status_leads_id', '!=', 10)
            ->where('status_leads_id', '!=', 1)
            ->where('status_leads_id', '!=', 11)
            ->count();
        $this->statusClosed   = ProspectLead::where('status_leads_id', 11)
            ->count();
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard');
    }
}
