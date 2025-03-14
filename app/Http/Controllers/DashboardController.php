<?php

namespace App\Http\Controllers;

use App\Models\ProspectLead;
use App\Models\StatusLead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $prospectCount = $this->countProspects();
        $schedules = \App\Models\ProspectLead::select('id', 'schedule', 'company_name', 'status_leads_id', 'followup_by')->get(); // Fetch schedule data with company_name

        // Aggregate count of status_leads_id
        $statusCounts = DB::table('prospect_leads')
            ->select('status', DB::raw('count(*) as count'))
            ->join('status_leads', 'prospect_leads.status_leads_id', '=', 'status_leads.id')
            ->groupBy('status')
            ->get();
        $categoryIndustries = DB::table('prospect_leads')
            ->select('category', DB::raw('count(*) as count'))
            ->join('category_industries', 'prospect_leads.category_industries_id', '=', 'category_industries.id')
            ->groupBy('category')
            ->get();
        $statusFailed = ProspectLead::where('status_leads_id', '10')->count();
        $statusFollowup = ProspectLead::where('status_leads_id', '!=', 10)
            ->where('status_leads_id', '!=', 1)
            ->where('status_leads_id', '!=', 11)
            ->count();
        $statusPendingFollowup = ProspectLead::where('status_leads_id', 1)
            ->count();
        $statusClosed = ProspectLead::where('status_leads_id', 11)
            ->count();
        $statusLeads = StatusLead::leftJoin('prospect_leads', 'status_leads.id', '=', 'prospect_leads.status_leads_id')
            ->select('status_leads.status', DB::raw('COUNT(prospect_leads.id) as total'))
            ->groupBy('status_leads.status')
            ->get();

        return view('dashboard', compact('prospectCount', 'schedules', 'statusCounts', 'statusLeads', 'statusFailed', 'statusFollowup', 'statusPendingFollowup', 'categoryIndustries', 'statusClosed')); // Pass statusCounts to view
    }

    public function getSchedules()
    {
        $schedules = \App\Models\ProspectLead::select('id', 'schedule', 'company_name', 'status_leads_id', 'followup_by')->get(); // Fetch schedule data with company_name
        return response()->json($schedules); // Return schedule data as JSON
    }

    public function countProspects()
    {
        $prospects = \App\Models\ProspectLead::all();
        return $prospects->count();
    }

    public function getActivities()
    {
        return view('activities');
    }


    public function getProspectLeads(Request $request)
    {

        $query = ProspectLead::with(['user', 'status_leads']);

        // Filter berdasarkan tanggal jika ada input
        if (!$request->start_date || !$request->end_date) {
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
        } else {
            // Jika ada filter tanggal, gunakan input dari user
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        }

        $query->whereBetween('updated_at', [$start, $end]);

        // Hitung jumlah leads berdasarkan status

        return DataTables::of($query)
            ->addColumn('updated_at', fn($lead) => $lead->updated_at ? $lead->updated_at->format('Y-m-d') : '')
            ->addColumn('user_name', fn($lead) => $lead->user->name ?? 'N/A')
            ->addColumn('status_name', fn($lead) => $lead->status_leads->status ?? 'N/A')
            ->addColumn('notes', fn($lead) => strip_tags($lead->notes))
            ->make(true);
    }
}
