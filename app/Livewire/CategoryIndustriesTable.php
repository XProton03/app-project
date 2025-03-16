<?php

namespace App\Livewire;

use App\Models\StatusLead;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CategoryIndustriesTable extends Component
{
    public $categoryIndustries = [];
    public $statusLeads = [];

    public function loadData()
    {
        $this->categoryIndustries = DB::table('prospect_leads')
            ->select('category_industries.category as category', DB::raw('count(*) as count'))
            ->join('category_industries', 'prospect_leads.category_industries_id', '=', 'category_industries.id')
            ->groupBy('category_industries.category')
            ->get();
        $this->statusLeads    = StatusLead::leftJoin('prospect_leads', 'status_leads.id', '=', 'prospect_leads.status_leads_id')
            ->select('status_leads.status', DB::raw('COUNT(prospect_leads.id) as total'))
            ->groupBy('status_leads.status')
            ->get();
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.category-industries-table');
    }
}
