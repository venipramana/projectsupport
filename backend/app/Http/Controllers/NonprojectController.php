<?php

namespace App\Http\Controllers;

use App\Models\Nonproject;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NonprojectController extends Controller
{
    public function index(Request $request)
    {
        $query = Nonproject::orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        $nonprojects = $query->paginate(25)->withQueryString();
        $penggunas = Pengguna::where('status', 1)->orderBy('nama', 'asc')->get();
        $searchKeyword = $request->search ?? '';

        return view('nonproject.index', compact('nonprojects', 'penggunas', 'searchKeyword'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan' => 'required|string|max:255',
            'tanggal'  => 'required',
        ], [
            'kegiatan.required' => 'Kegiatan wajib diisi.',
            'tanggal.required'  => 'Tanggal wajib diisi.',
        ]);

        $data = $request->all();
        $data['tanggal'] = $this->formatTanggalToYmd($data['tanggal']);

        Nonproject::create($data);

        return redirect()->route('nonproject.index')->with('success', 'Data Non Project berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kegiatan' => 'required|string|max:255',
            'tanggal'  => 'required',
        ], [
            'kegiatan.required' => 'Kegiatan wajib diisi.',
            'tanggal.required'  => 'Tanggal wajib diisi.',
        ]);

        $nonproject = Nonproject::findOrFail($id);
        $data = $request->all();
        $data['tanggal'] = $this->formatTanggalToYmd($data['tanggal']);

        $nonproject->update($data);

        return redirect()->route('nonproject.index')->with('success', 'Data Non Project berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $nonproject = Nonproject::findOrFail($id);
        $nonproject->delete();

        return redirect()->route('nonproject.index')->with('success', 'Data Non Project berhasil dihapus.');
    }

    /**
     * Convert date string from datepicker (dd-mm-yyyy or Y-m-d) to 'yyyymmdd' format
     */
    private function formatTanggalToYmd($dateStr)
    {
        if (empty($dateStr)) {
            return date('Ymd');
        }

        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateStr)) {
                return Carbon::createFromFormat('d-m-Y', $dateStr)->format('Ymd');
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return Carbon::createFromFormat('Y-m-d', $dateStr)->format('Ymd');
            } else {
                return preg_replace('/[^0-9]/', '', $dateStr);
            }
        } catch (\Exception $e) {
            return date('Ymd');
        }
    }
}
