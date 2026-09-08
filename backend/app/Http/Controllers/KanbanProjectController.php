<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Hproject;
use App\Models\Rproject;
use App\Models\Rdirektorat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KanbanProjectController extends Controller
{
    /**
     * Display the Kanban Progress matrix view.
     */
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $selectedTahun = $request->has('filter_tahun') ? $request->filter_tahun : $currentYear;
        $selectedDirektorat = $request->filter_direktorat ?? '';
        $searchKeyword = $request->search ?? '';

        $query = Project::with([
            'direktorat_rel',
            'rproject_relation',
            'leadby_rel',
            'supportby_rel',
            'vul_passed_rel',
            'rkap_rel',
            'catalog_rel'
        ]);

        // Filter Tahun berdasarkan tanggal_awal (default current year jika tidak ada parameter)
        if (!empty($selectedTahun) && $selectedTahun !== 'all') {
            $query->where(function($q) use ($selectedTahun) {
                $q->whereYear('tanggal_awal', $selectedTahun)
                  ->orWhere(function($sub) use ($selectedTahun) {
                      $sub->whereNull('tanggal_awal')
                          ->whereYear('tanggal', $selectedTahun);
                  });
            });
        }

        // Filter Direktorat
        if (!empty($selectedDirektorat)) {
            $query->where('direktorat', $selectedDirektorat);
        }

        // Search Filter
        if (!empty($searchKeyword)) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('project_name', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('no_surat', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('pic_name', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('bagian', 'like', '%' . $searchKeyword . '%');
            });
        }

        $projects = $query->orderBy('id', 'desc')->get();

        // Ambil daftar step dinamis dari rproject
        $rprojects = Rproject::orderBy('id', 'asc')->get();

        // Ambil daftar direktorat untuk filter
        $direktorats = Rdirektorat::all();

        // Ambil list tahun unik dari tanggal_awal & tanggal
        $tahunAwalList = Project::selectRaw('YEAR(tanggal_awal) as year')
            ->whereNotNull('tanggal_awal')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $tahunTanggalList = Project::selectRaw('YEAR(tanggal) as year')
            ->whereNotNull('tanggal')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $tahunList = array_unique(array_filter(array_merge([$currentYear], $tahunAwalList, $tahunTanggalList)));
        rsort($tahunList);

        // Ringkasan KPI
        $totalProjects = $projects->count();
        $liveStatusIds = Rproject::where('deskripsi', 'like', '%live%')
            ->orWhere('deskripsi', 'like', '%selesai%')
            ->orWhere('deskripsi', 'like', '%closed%')
            ->pluck('id')
            ->toArray();
        $completedProjects = $projects->whereIn('rproject', $liveStatusIds)->count();
        $inProgressProjects = $totalProjects - $completedProjects;

        return view('kanban.index', compact(
            'projects',
            'rprojects',
            'direktorats',
            'tahunList',
            'selectedTahun',
            'selectedDirektorat',
            'searchKeyword',
            'totalProjects',
            'completedProjects',
            'inProgressProjects'
        ));
    }

    /**
     * Update project step, log to hproject, and upload evidence file to MinIO.
     */
    public function updateStep(Request $request)
    {
        $request->validate([
            'idproject' => 'required|integer|exists:project,id',
            'rproject' => 'required|integer|exists:rproject,id',
            'tanggal' => 'required|string',
            'catatan' => 'required|string|max:500',
            'progress' => 'nullable|integer|min:0|max:100',
            'evidence_file' => 'nullable|file|max:51200'
        ], [
            'idproject.required' => 'Project ID tidak valid.',
            'rproject.required' => 'Target step Kanban harus dipilih.',
            'tanggal.required' => 'Tanggal perubahan status wajib diisi.',
            'catatan.required' => 'Catatan perubahan status wajib diisi.',
            'evidence_file.max' => 'Ukuran file evidence maksimal 50 MB.'
        ]);

        $project = Project::findOrFail($request->idproject);

        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke project ini.'], 403);
            }
            abort(403, 'Unauthorized.');
        }

        // Convert tanggal ke Ymd
        $tanggalFormatted = date('Ymd');
        if (!empty($request->tanggal)) {
            try {
                $tanggalFormatted = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Ymd');
            } catch (\Exception $e) {
                try {
                    $tanggalFormatted = Carbon::parse($request->tanggal)->format('Ymd');
                } catch (\Exception $ex) {
                    $tanggalFormatted = date('Ymd');
                }
            }
        }

        // Upload evidence ke MinIO jika ada
        $uploadedFileMsg = '';
        if ($request->hasFile('evidence_file')) {
            try {
                if (empty($project->folder_evidence)) {
                    $project->folder_evidence = "evidence_project_" . $project->id;
                    $project->save();
                }

                $disk = Storage::disk('minio');
                if (!$disk->exists($project->folder_evidence . '/.keep')) {
                    $disk->put($project->folder_evidence . '/.keep', '');
                }

                $file = $request->file('evidence_file');
                $filename = $file->getClientOriginalName();
                $disk->putFileAs($project->folder_evidence, $file, $filename);
                $uploadedFileMsg = " File evidence (\"{$filename}\") berhasil disimpan ke MinIO.";
            } catch (\Exception $e) {
                Log::error("MinIO upload error in KanbanProjectController: " . $e->getMessage());
            }
        }

        // 1. Simpan ke tabel hproject
        Hproject::create([
            'idproject' => $project->id,
            'rproject' => $request->rproject,
            'tanggal' => $tanggalFormatted,
            'catatan' => $request->catatan,
            'progress' => $request->progress ?? 0,
        ]);

        // 2. Update tabel project
        $targetStep = Rproject::find($request->rproject);
        $project->update([
            'rproject' => $request->rproject,
            'tgl_update' => date('Y-m-d')
        ]);

        $stepName = $targetStep ? $targetStep->deskripsi : 'Step #' . $request->rproject;
        $successMessage = "Step Kanban project \"{$project->project_name}\" berhasil diubah menjadi \"{$stepName}\".{$uploadedFileMsg}";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'project_id' => $project->id,
                'rproject_id' => $project->rproject,
                'step_name' => $stepName
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }
}
