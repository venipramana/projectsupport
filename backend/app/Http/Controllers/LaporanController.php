<?php

namespace App\Http\Controllers;

use App\Models\ViewProgress;
use App\Models\Rdirektorat;
use App\Models\Rproject;
use App\Models\Rrkap;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function progressIndex(Request $request)
    {
        $query = ViewProgress::query();

        if ($request->filled('search')) {
            $query->where('project_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('direktorat')) {
            $query->where('direktorat', $request->direktorat);
        }

        if ($request->filled('progress')) {
            $query->where('rproject', $request->progress);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_awal', $request->tahun);
        }

        if ($request->filled('rkap')) {
            $query->where('rkap', $request->rkap);
        }

        if ($request->filled('pic')) {
            // Memfilter berdasarkan assign_to_name
            $query->where('assign_to_name', $request->pic);
        }

        $data = $query->orderBy('tgl_update', 'desc')->get();

        $total_projects = $data->count();
        $total_bsurkap = $data->sum('bsurkap');

        $direktoratOptions = Rdirektorat::orderBy('deskripsi')->get();
        $progressOptions = Rproject::orderBy('deskripsi')->get();
        $rkapOptions = Rrkap::orderBy('nama_rkap')->get();
        $picOptions = Pengguna::orderBy('nama')->get();
        $tahunOptions = DB::table('view_progress')
                        ->select(DB::raw('YEAR(tanggal_awal) as tahun'))
                        ->whereNotNull('tanggal_awal')
                        ->distinct()
                        ->orderBy('tahun', 'desc')
                        ->pluck('tahun');

        return view('laporan.progress.index', compact(
            'data', 'total_projects', 'total_bsurkap',
            'direktoratOptions', 'progressOptions', 'rkapOptions', 'picOptions', 'tahunOptions'
        ));
    }
}
