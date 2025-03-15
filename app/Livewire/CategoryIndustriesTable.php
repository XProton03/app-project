<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CategoryIndustriesTable extends Component
{
    public $categoryIndustries = []; // Inisialisasi variabel

    // public function mount()
    // {
    //     $this->loadData();
    // }

    public function loadData()
    {
        $this->categoryIndustries = DB::table('prospect_leads')
            ->select('category_industries.category as category', DB::raw('count(*) as count'))
            ->join('category_industries', 'prospect_leads.category_industries_id', '=', 'category_industries.id')
            ->groupBy('category_industries.category')
            ->get();
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.category-industries-table');
    }
}
