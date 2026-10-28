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

        // Semua nilai filter dinormalisasi ke string. Ini mencegah perbandingan
        // longgar (==) di view salah memilih opsi, terutama untuk status
        // "ONHOLD" yang id rproject-nya bernilai 0 (0 == '' / 0 == null).
        $selectedSearch = (string) $request->query('search', '');
        $selectedDirektorat = (string) $request->query('direktorat', '');
        $selectedProgress = (string) $request->query('progress', '');
        $selectedTahun = (string) $request->query('tahun', '');
        $selectedBulan = (string) $request->query('bulan', '');
        $selectedRkap = (string) $request->query('rkap', '');
        $selectedPic = (string) $request->query('pic', '');

        if ($selectedSearch !== '') {
            $query->where('project_name', 'like', '%' . $selectedSearch . '%');
        }

        if ($selectedDirektorat !== '') {
            $query->where('direktorat', $selectedDirektorat);
        }

        // Filter status project (rproject).
        // Catatan: id rproject = 0 berarti "ONHOLD", jadi nilai "0" yang sah
        // (bukan filter kosong) tetap harus bisa dipakai.
        if ($selectedProgress !== '') {
            $query->where('rproject', $selectedProgress);
        }

        if ($selectedTahun !== '') {
            $query->whereYear('tanggal_awal', $selectedTahun);
        }

        if ($selectedBulan !== '') {
            $query->whereMonth('tanggal_awal', $selectedBulan);
        }

        if ($selectedRkap !== '') {
            $query->where('rkap', $selectedRkap);
        }

        if ($selectedPic !== '') {
            // Memfilter berdasarkan assign_to_name
            $query->where('assign_to_name', $selectedPic);
        }

        // Hitung total agregat dari seluruh data terfilter sebelum paginasi
        $total_projects = (clone $query)->count();
        $total_bsurkap = (clone $query)->sum('bsurkap');

        // Data lengkap terfilter untuk Excel export seluruh baris
        $allData = (clone $query)->orderBy('tgl_update', 'desc')->get();

        $data = $query->orderBy('tgl_update', 'desc')->paginate(25)->withQueryString();

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
            'data', 'allData', 'total_projects', 'total_bsurkap',
            'direktoratOptions', 'progressOptions', 'rkapOptions', 'picOptions', 'tahunOptions', 'bulanOptions',
            'selectedSearch', 'selectedDirektorat', 'selectedProgress',
            'selectedTahun', 'selectedBulan', 'selectedRkap', 'selectedPic'
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

        // Filter Tahun & Bulan dengan Indexed Prefix LIKE (memanfaatkan B-Tree Index)
        if ($request->filled('tahun') && $request->filled('bulan')) {
            $bulanPad = sprintf('%02d', $request->bulan);
            $query->where('tanggal', 'like', $request->tahun . $bulanPad . '%');
        } elseif ($request->filled('tahun')) {
            $query->where('tanggal', 'like', $request->tahun . '%');
        } elseif ($request->filled('bulan')) {
            $bulanPad = sprintf('%02d', $request->bulan);
            $query->where('tanggal', 'like', '____' . $bulanPad . '%');
        }

        // Filter PIC
        if ($request->filled('pic')) {
            $query->where('pic', $request->pic);
        }

        // Hitung total dari keseluruhan data terfilter sebelum paginasi
        $total_kegiatan = (clone $query)->count();

        // Data lengkap terfilter untuk Excel export seluruh baris
        $allData = (clone $query)->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        $data = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();

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
            'data', 'allData', 'total_kegiatan', 'tahunOptions', 'bulanOptions', 'picOptions'
        ));
    }

    public function kegiatanBulanan(Request $request)
    {
        $selectedTahun = $request->has('tahun') ? (string) $request->query('tahun', '') : (string) date('Y');
        $selectedBulan = $request->has('bulan') ? (string) $request->query('bulan', '') : (string) (int) date('m');
        $selectedSearch = (string) $request->query('search', '');
        $selectedSumber = (string) $request->query('sumber', '');

        // Query dari hproject
        $hprojectQuery = DB::table('hproject')
            ->leftJoin('project', 'hproject.idproject', '=', 'project.id')
            ->leftJoin('rproject', 'hproject.rproject', '=', 'rproject.id')
            ->select([
                DB::raw("'project' COLLATE utf8mb4_general_ci as sumber"),
                'hproject.id as ref_id',
                DB::raw('CAST(hproject.tanggal AS CHAR) COLLATE utf8mb4_general_ci as tanggal'),
                DB::raw("COALESCE(project.project_name, CONCAT('Project #', hproject.idproject)) COLLATE utf8mb4_general_ci as project_kegiatan"),
                DB::raw("COALESCE(rproject.deskripsi, '-') COLLATE utf8mb4_general_ci as deskripsi_kegiatan"),
                DB::raw("COALESCE(hproject.catatan, '') COLLATE utf8mb4_general_ci as catatan"),
            ]);

        // Query dari nonproject
        $nonprojectQuery = DB::table('nonproject')
            ->select([
                DB::raw("'nonproject' COLLATE utf8mb4_general_ci as sumber"),
                'nonproject.id as ref_id',
                DB::raw("(CASE WHEN CHAR_LENGTH(tanggal) = 8 THEN CONCAT(SUBSTRING(tanggal, 1, 4), '-', SUBSTRING(tanggal, 5, 2), '-', SUBSTRING(tanggal, 7, 2)) ELSE tanggal END) COLLATE utf8mb4_general_ci as tanggal"),
                DB::raw("'LAIN-LAIN' COLLATE utf8mb4_general_ci as project_kegiatan"),
                DB::raw("COALESCE(kegiatan, '-') COLLATE utf8mb4_general_ci as deskripsi_kegiatan"),
                DB::raw("COALESCE(catatan, '') COLLATE utf8mb4_general_ci as catatan"),
            ]);

        // Filter hproject berdasarkan tahun & bulan
        if ($selectedTahun !== '') {
            $hprojectQuery->whereYear('hproject.tanggal', $selectedTahun);
        }
        if ($selectedBulan !== '') {
            $hprojectQuery->whereMonth('hproject.tanggal', $selectedBulan);
        }

        // Filter nonproject berdasarkan tahun & bulan
        if ($selectedTahun !== '' && $selectedBulan !== '') {
            $bulanPad = sprintf('%02d', (int) $selectedBulan);
            $nonprojectQuery->where(function ($q) use ($selectedTahun, $bulanPad) {
                $q->where('nonproject.tanggal', 'like', $selectedTahun . $bulanPad . '%')
                  ->orWhere('nonproject.tanggal', 'like', $selectedTahun . '-' . $bulanPad . '%');
            });
        } elseif ($selectedTahun !== '') {
            $nonprojectQuery->where(function ($q) use ($selectedTahun) {
                $q->where('nonproject.tanggal', 'like', $selectedTahun . '%');
            });
        } elseif ($selectedBulan !== '') {
            $bulanPad = sprintf('%02d', (int) $selectedBulan);
            $nonprojectQuery->where(function ($q) use ($bulanPad) {
                $q->where('nonproject.tanggal', 'like', '____' . $bulanPad . '%')
                  ->orWhere('nonproject.tanggal', 'like', '____-' . $bulanPad . '%');
            });
        }

        // Satukan kedua query menggunakan unionAll dan subquery wrapper
        $combined = $hprojectQuery->unionAll($nonprojectQuery);
        $outerQuery = DB::query()->fromSub($combined, 'combined_kegiatan');

        if ($selectedSumber !== '') {
            $outerQuery->where('sumber', $selectedSumber);
        }

        if ($selectedSearch !== '') {
            $outerQuery->where(function ($q) use ($selectedSearch) {
                $q->where('project_kegiatan', 'like', '%' . $selectedSearch . '%')
                  ->orWhere('deskripsi_kegiatan', 'like', '%' . $selectedSearch . '%')
                  ->orWhere('catatan', 'like', '%' . $selectedSearch . '%');
            });
        }

        // Hitung total ringkasan
        $total_kegiatan = (clone $outerQuery)->count();
        $total_project = (clone $outerQuery)->where('sumber', 'project')->count();
        $total_nonproject = (clone $outerQuery)->where('sumber', 'nonproject')->count();

        // Data lengkap untuk ekspor Excel (Project didahulukan, lalu di-sort berdasarkan project/kegiatan)
        $allData = (clone $outerQuery)
            ->orderByRaw("CASE WHEN sumber = 'project' THEN 1 ELSE 2 END ASC")
            ->orderBy('project_kegiatan', 'asc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('ref_id', 'asc')
            ->get();

        // Data paginasi untuk tampilan tabel
        $data = $outerQuery
            ->orderByRaw("CASE WHEN sumber = 'project' THEN 1 ELSE 2 END ASC")
            ->orderBy('project_kegiatan', 'asc')
            ->orderBy('tanggal', 'asc')
            ->orderBy('ref_id', 'asc')
            ->paginate(25)
            ->withQueryString();

        // Ambil daftar tahun unik dari kedua tabel
        $hYears = DB::table('hproject')
            ->select(DB::raw('YEAR(tanggal) as tahun'))
            ->whereNotNull('tanggal')
            ->distinct()
            ->pluck('tahun')
            ->map(fn($y) => (string) $y);

        $nYears = DB::table('nonproject')
            ->select(DB::raw('SUBSTRING(tanggal, 1, 4) as tahun'))
            ->whereNotNull('tanggal')
            ->whereRaw('CHAR_LENGTH(tanggal) >= 4')
            ->distinct()
            ->pluck('tahun')
            ->map(fn($y) => (string) $y);

        $currentYear = (string) date('Y');
        $tahunOptions = $hYears->merge($nYears)->push($currentYear)->filter()->unique()->sortDesc()->values();

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

        return view('laporan.kegiatan_bulanan.index', compact(
            'data', 'allData', 'total_kegiatan', 'total_project', 'total_nonproject',
            'tahunOptions', 'bulanOptions',
            'selectedTahun', 'selectedBulan', 'selectedSearch', 'selectedSumber'
        ));
    }
}

