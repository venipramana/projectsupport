<?php

namespace App\Http\Controllers;

use App\Models\ViewProgress;
use App\Models\Rdirektorat;
use App\Models\Rproject;
use App\Models\Rrkap;
use App\Models\Pengguna;
use App\Models\Nonproject;
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

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_awal', $request->bulan);
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

        $bulanOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return view('laporan.progress.index', compact(
            'data', 'total_projects', 'total_bsurkap',
            'direktoratOptions', 'progressOptions', 'rkapOptions', 'picOptions', 'tahunOptions', 'bulanOptions'
        ));
    }

    public function nonprojectIndex(Request $request)
    {
        $query = Nonproject::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // Filter Tahun (extract from YYYYMMDD string format)
        if ($request->filled('tahun')) {
            $query->whereRaw("SUBSTRING(tanggal, 1, 4) = ?", [$request->tahun]);
        }

        // Filter Bulan (extract from YYYYMMDD string format)
        if ($request->filled('bulan')) {
            $bulanPad = sprintf('%02d', $request->bulan);
            $query->whereRaw("SUBSTRING(tanggal, 5, 2) = ?", [$bulanPad]);
        }

        // Filter PIC
        if ($request->filled('pic')) {
            $query->where('pic', $request->pic);
        }

        $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        $total_kegiatan = $data->count();

        // Extract list of years available in nonproject table
        $tahunOptions = DB::table('nonproject')
            ->select(DB::raw("DISTINCT SUBSTRING(tanggal, 1, 4) as tahun"))
            ->whereNotNull('tanggal')
            ->whereRaw("CHAR_LENGTH(tanggal) >= 4")
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $bulanOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $picOptions = Pengguna::where('status', 1)->orderBy('nama', 'asc')->get();

        return view('laporan.nonproject.index', compact(
            'data', 'total_kegiatan', 'tahunOptions', 'bulanOptions', 'picOptions'
        ));
    }
}
