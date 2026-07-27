<?php

namespace App\Http\Controllers;

use App\Models\Rdirektorat;
use Illuminate\Http\Request;

class RdirektoratController extends Controller
{
    public function index()
    {
        $rdirektorats = Rdirektorat::orderBy('id', 'desc')->get();
        return view('rdirektorat.index', compact('rdirektorats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:150',
            'status' => 'nullable|integer',
            'cluster' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['status'] = $data['status'] ?? 0;

        Rdirektorat::create($data);

        return redirect()->route('rdirektorat.index')->with('success', 'Direktorat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rdirektorat = Rdirektorat::findOrFail($id);

        $request->validate([
            'deskripsi' => 'required|string|max:150',
            'status' => 'nullable|integer',
            'cluster' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        if (!isset($data['status'])) $data['status'] = 0;

        $rdirektorat->update($data);

        return redirect()->route('rdirektorat.index')->with('success', 'Direktorat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rdirektorat = Rdirektorat::findOrFail($id);
        $rdirektorat->delete();

        return redirect()->route('rdirektorat.index')->with('success', 'Direktorat berhasil dihapus.');
    }
}
