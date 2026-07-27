<?php

namespace App\Http\Controllers;

use App\Models\Rrkap;
use Illuminate\Http\Request;

class RrkapController extends Controller
{
    public function index()
    {
        $rrkaps = Rrkap::orderBy('idrkap', 'desc')->get();
        return view('rrkap.index', compact('rrkaps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idrkap' => 'required|integer|unique:rrkap,idrkap',
            'tahun_rkap' => 'nullable|string|max:4',
            'nama_rkap' => 'nullable|string|max:255',
            'kode_rkap' => 'nullable|string|max:255',
            'bsu_rkap' => 'nullable|numeric',
        ]);

        Rrkap::create($request->all());

        return redirect()->route('rrkap.index')->with('success', 'RKAP berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rrkap = Rrkap::findOrFail($id);

        $request->validate([
            'idrkap' => 'required|integer|unique:rrkap,idrkap,' . $id . ',idrkap',
            'tahun_rkap' => 'nullable|string|max:4',
            'nama_rkap' => 'nullable|string|max:255',
            'kode_rkap' => 'nullable|string|max:255',
            'bsu_rkap' => 'nullable|numeric',
        ]);

        $rrkap->update($request->all());

        return redirect()->route('rrkap.index')->with('success', 'RKAP berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rrkap = Rrkap::findOrFail($id);
        $rrkap->delete();

        return redirect()->route('rrkap.index')->with('success', 'RKAP berhasil dihapus.');
    }
}
