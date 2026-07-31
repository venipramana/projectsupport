<?php

namespace App\Http\Controllers;

use App\Models\Hproject;
use App\Models\Project;
use App\Models\Rproject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

        $evidenceFiles = [];
        if (!empty($project->folder_evidence)) {
            try {
                $disk = Storage::disk('minio');
                $files = $disk->files($project->folder_evidence);
                foreach ($files as $file) {
                    $fileName = basename($file);
                    if ($fileName === '.keep') continue;
                    
                    $size = $disk->size($file);
                    $sizeStr = $size >= 1048576 
                        ? round($size / 1048576, 2) . ' MB' 
                        : ($size >= 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B');

                    $lastModified = $disk->lastModified($file);

                    $evidenceFiles[] = [
                        'name' => $fileName,
                        'path' => $file,
                        'size' => $sizeStr,
                        'date' => date('d-m-Y H:i', $lastModified),
                    ];
                }
            } catch (\Exception $e) {
                Log::error("MinIO error listing evidence: " . $e->getMessage());
            }
        }

        return view('hproject.index', compact('project', 'hprojects', 'rprojects', 'evidenceFiles'));
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

    /**
     * Store multiple uploaded evidence files to MinIO.
     */
    public function storeEvidence(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'evidences' => 'required|array',
            'evidences.*' => 'required|file|max:51200' // Maks 50MB per file
        ]);

        if (empty($project->folder_evidence)) {
            $project->folder_evidence = "evidence_project_" . $project->id;
            $project->save();
        }

        $disk = Storage::disk('minio');
        if (!$disk->exists($project->folder_evidence . '/.keep')) {
            $disk->put($project->folder_evidence . '/.keep', '');
        }

        $uploadCount = 0;
        if ($request->hasFile('evidences')) {
            foreach ($request->file('evidences') as $file) {
                $filename = $file->getClientOriginalName();
                $disk->putFileAs($project->folder_evidence, $file, $filename);
                $uploadCount++;
            }
        }

        return redirect()->route('hproject.index', $project_id)
            ->with('success', "Berhasil mengupload {$uploadCount} file evidence ke MinIO.");
    }

    /**
     * Stream evidence file from MinIO for viewing in browser.
     */
    public function viewEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if (!$disk->exists($path)) {
            abort(404, 'File evidence tidak ditemukan di MinIO.');
        }

        return $disk->response($path);
    }

    /**
     * Download evidence file from MinIO.
     */
    public function downloadEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if (!$disk->exists($path)) {
            abort(404, 'File evidence tidak ditemukan di MinIO.');
        }

        return $disk->download($path, $filename);
    }

    /**
     * Delete evidence file from MinIO.
     */
    public function destroyEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if ($disk->exists($path)) {
            $disk->delete($path);

            $remainingFiles = $disk->files($project->folder_evidence);
            if (empty($remainingFiles) || (count($remainingFiles) === 1 && basename($remainingFiles[0]) === '.keep')) {
                if (!$disk->exists($project->folder_evidence . '/.keep')) {
                    $disk->put($project->folder_evidence . '/.keep', '');
                }
            }

            return redirect()->route('hproject.index', $project_id)
                ->with('success', "File evidence \"{$filename}\" berhasil dihapus dari MinIO.");
        }

        return redirect()->route('hproject.index', $project_id)
            ->with('error', "File evidence tidak ditemukan di storage.");
    }
}
