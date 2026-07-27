<?php

namespace App\Http\Controllers;

use App\Models\Rproject;
use Illuminate\Http\Request;

class RprojectController extends Controller
{
    public function index()
    {
        $rprojects = Rproject::orderBy('id', 'desc')->get();
        return view('rproject.index', compact('rprojects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:150',
            'icon' => 'required|string|max:50',
            'status' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['status'] = $data['status'] ?? 0;

        Rproject::create($data);

        return redirect()->route('rproject.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rproject = Rproject::findOrFail($id);

        $request->validate([
            'deskripsi' => 'required|string|max:150',
            'icon' => 'required|string|max:50',
            'status' => 'nullable|integer',
        ]);

        $data = $request->all();
        if (!isset($data['status'])) $data['status'] = 0;

        $rproject->update($data);

        return redirect()->route('rproject.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rproject = Rproject::findOrFail($id);
        $rproject->delete();

        return redirect()->route('rproject.index')->with('success', 'Project berhasil dihapus.');
    }
}
