<?php

namespace App\Http\Controllers;

use App\Models\Hproject;
use App\Models\Project;
use App\Models\Rproject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HprojectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($idproject)
    {
        $project = Project::with('direktorat_rel', 'rkap_rel')->findOrFail($idproject);
        
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }
        
        $hprojects = Hproject::with('rproject_rel')
            ->where('idproject', $idproject)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $rprojects = Rproject::all();

        return view('hproject.index', compact('project', 'hprojects', 'rprojects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idproject' => 'required|integer',
            'rproject' => 'required|integer',
            'tanggal' => 'required|string',
            'catatan' => 'required|string|max:150',
            'progress' => 'nullable|integer'
        ]);

        $project = Project::findOrFail($request->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->all();
        
        // Convert dd-mm-yyyy to YYYYMMDD
        if (!empty($data['tanggal'])) {
            try {
                $data['tanggal'] = Carbon::createFromFormat('d-m-Y', $data['tanggal'])->format('Ymd');
            } catch (\Exception $e) {
                // Ignore formatting error, let DB handle
            }
        }

        Hproject::create($data);

        // Update parent project status
        $project = Project::find($data['idproject']);
        if ($project) {
            $project->update(['rproject' => $data['rproject']]);
        }

        return redirect()->route('hproject.index', $data['idproject'])->with('success', 'Progress project berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rproject' => 'required|integer',
            'tanggal' => 'required|string',
            'catatan' => 'required|string|max:150',
            'progress' => 'nullable|integer'
        ]);

        $hproject = Hproject::findOrFail($id);
        $project = Project::findOrFail($hproject->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->all();

        // Convert dd-mm-yyyy to YYYYMMDD
        if (!empty($data['tanggal'])) {
            try {
                $data['tanggal'] = Carbon::createFromFormat('d-m-Y', $data['tanggal'])->format('Ymd');
            } catch (\Exception $e) {
                // Ignore formatting error, let DB handle
            }
        }

        $hproject->update($data);

        // Update parent project status
        $project = Project::find($hproject->idproject);
        if ($project) {
            $project->update(['rproject' => $data['rproject']]);
        }

        return redirect()->route('hproject.index', $hproject->idproject)->with('success', 'Progress project berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hproject = Hproject::findOrFail($id);
        $project = Project::findOrFail($hproject->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }
        $idproject = $hproject->idproject;
        $hproject->delete();

        return redirect()->route('hproject.index', $idproject)->with('success', 'Progress project berhasil dihapus.');
    }
}
