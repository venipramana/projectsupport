<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViewProgress;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1: Total seluruh project
        $total_projects = ViewProgress::count();

        // 2: Summary project per-direktorat (untuk donut chart)
        $projects_by_direktorat = ViewProgress::select('direktorat', DB::raw('count(*) as total'))
            ->whereNotNull('direktorat')
            ->groupBy('direktorat')
            ->orderByDesc('total')
            ->get();

        // 3: List summary project per-direktorat tahun ini
        $currentYear = date('Y');
        $projects_this_year = ViewProgress::whereYear('tanggal_awal', $currentYear)
            ->whereNotNull('direktorat')
            ->select('direktorat', DB::raw('count(*) as total'))
            ->groupBy('direktorat')
            ->orderByDesc('total')
            ->get();

        // Summary project per Lead By (assign_to_name)
        $projects_by_lead = ViewProgress::select(
                DB::raw("CASE WHEN assign_to_name IS NULL OR TRIM(assign_to_name) = '' THEN 'Belum Ditugaskan' ELSE assign_to_name END as lead_by"),
                DB::raw('count(*) as total')
            )
            ->groupBy('lead_by')
            ->orderByDesc('total')
            ->get();

        // 4: List project dengan status development (rproject = 1)
        $dev_projects = ViewProgress::where('rproject', 1)
            ->orderBy('tgl_update', 'desc')
            ->get();

        // 5: List project dengan status QA (rproject = 3)
        $qa_projects = ViewProgress::where('rproject', 3)
            ->orderBy('tgl_update', 'desc')
            ->get();

        // Data lengkap untuk filtering Detil di Frontend via Javascript
        $all_projects = ViewProgress::orderBy('tgl_update', 'desc')->get();

        return view('dashboard', compact(
            'total_projects',
            'projects_by_direktorat',
            'projects_this_year',
            'projects_by_lead',
            'dev_projects',
            'qa_projects',
            'all_projects',
            'currentYear'
        ));
    }
}
