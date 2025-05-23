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
        return view('dashboard'); // Pass statusCounts to view
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

    public function getProspect()
    {
        return view('prospect');
    }
    public function getDataProspect(Request $request)
    {
        $query = ProspectLead::with(['user', 'status_leads']);

        return DataTables::of($query)
            ->addColumn('updated_at_formatted', function ($lead) {
                return $lead->updated_at ? $lead->updated_at->format('Y-m-d') : '';
            })
            ->editColumn('last_followup', function ($lead) {
                return $lead->last_followup ? $lead->last_followup->format('Y-m-d') : '-';
            })
            ->addColumn('user_name', function ($lead) {
                return $lead->user->name ?? '-';
            })
            ->addColumn('status_name', function ($lead) {
                return $lead->status_leads->status ?? '-';
            })
            ->addColumn('notes', function ($lead) {
                return strip_tags($lead->notes);
            })
            ->orderColumn('updated_at', 'updated_at $1') // Memastikan sorting berdasarkan nilai asli
            ->filterColumn('updated_at', function ($query, $keyword) {
                $query->whereRaw("DATE(updated_at) LIKE ?", ["%{$keyword}%"]);
            })
            ->rawColumns(['updated_at_formatted']) // Pastikan HTML dalam kolom ini diproses
            ->make(true);
    }
}
