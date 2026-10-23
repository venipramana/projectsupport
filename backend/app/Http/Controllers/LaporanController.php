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
}
