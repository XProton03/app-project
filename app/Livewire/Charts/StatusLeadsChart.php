<?php

namespace App\Livewire\Charts;

use Log;
use Livewire\Component;
use App\Models\StatusLead;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class StatusLeadsChart extends Component
{
    public $labels = [];
    public $data = [];
    public $categoryIndustries = [];

    protected $listeners = ['refreshChart' => 'loadChartData'];

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $this->categoryIndustries = DB::table('prospect_leads')
            ->select('category_industries.category as category', DB::raw('count(*) as count'))
            ->join('category_industries', 'prospect_leads.category_industries_id', '=', 'category_industries.id')
            ->groupBy('category_industries.category')
            ->get();
        // Ambil data dari database
        $statusLeads = StatusLead::leftJoin('prospect_leads', 'status_leads.id', '=', 'prospect_leads.status_leads_id')
            ->select('status_leads.status', DB::raw('COUNT(prospect_leads.id) as total'))
            ->groupBy('status_leads.status')
            ->get();

        // Simpan data ke dalam variabel
        $this->labels = $statusLeads->pluck('status')->toArray();
        $this->data = $statusLeads->pluck('total')->toArray();
    }

    public function render()
    {
        return view('livewire.charts.status-leads-chart');
    }
}
