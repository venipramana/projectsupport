<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $penggunas = Pengguna::orderBy('idtable', 'desc')->get();
        return view('pengguna.index', compact('penggunas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idpengguna' => 'required|unique:pengguna,idpengguna',
            'password' => 'required|min:6',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'kodelokasi' => 'nullable|string|max:255',
            'levelpengguna' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'bypass' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        
        // Ensure status and bypass default to 0 if not provided
        $data['status'] = $data['status'] ?? 0;
        $data['bypass'] = $data['bypass'] ?? 0;

        Pengguna::create($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $request->validate([
            'idpengguna' => 'required|unique:pengguna,idpengguna,' . $pengguna->idtable . ',idtable',
            'password' => 'nullable|min:6',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $pengguna->idtable . ',idtable',
            'kodelokasi' => 'nullable|string|max:255',
            'levelpengguna' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'bypass' => 'nullable|integer',
        ]);

        $data = $request->except(['password']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle nullables explicitly if they might be omitted in the request
        if (!isset($data['status'])) $data['status'] = 0;
        if (!isset($data['bypass'])) $data['bypass'] = 0;

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 400);
        }

        // Update password baru
        $pengguna = Pengguna::find($user->idtable);
        $pengguna->password = Hash::make($request->new_password);
        $pengguna->save();

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
    }
}
