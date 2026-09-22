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
        $selectedStatus = $request->filter_status ?? 'all';
        $selectedSort = $request->sort_by ?? 'dev_first';
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

        // Filter Tahun berdasarkan tanggal_akhir (default current year jika tidak ada parameter)
        if (!empty($selectedTahun) && $selectedTahun !== 'all') {
            $query->where(function($q) use ($selectedTahun) {
                $q->whereYear('tanggal_akhir', $selectedTahun)
                  ->orWhere(function($sub) use ($selectedTahun) {
                      $sub->whereNull('tanggal_akhir')
                          ->whereYear('tanggal', $selectedTahun);
                  });
            });
        }

        // Filter Direktorat
        if (!empty($selectedDirektorat)) {
            $query->where('direktorat', $selectedDirektorat);
        }

        // Filter Status / Step
        if (!empty($selectedStatus) && $selectedStatus !== 'all') {
            if ($selectedStatus === 'dev') {
                $query->where('rproject', 1);
            } elseif ($selectedStatus === 'progress') {
                $query->whereIn('rproject', [1, 2, 3, 4, 5]);
            } elseif ($selectedStatus === 'live') {
                $query->where('rproject', 6);
            } elseif (is_numeric($selectedStatus)) {
                $query->where('rproject', (int)$selectedStatus);
            }
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

        // Sorting: Default 'dev_first' prioritizes DEVELOPMENT (rproject = 1) at the very top
        if ($selectedSort === 'dev_first') {
            $query->orderByRaw("CASE 
                WHEN rproject = 1 THEN 1 
                WHEN rproject IN (2, 3, 4, 5) THEN 2 
                WHEN rproject = 6 THEN 4 
                ELSE 3 
            END ASC")
            ->orderBy('id', 'desc');
        } elseif ($selectedSort === 'progress_first') {
            $query->orderByRaw("CASE 
                WHEN rproject IN (1, 2, 3, 4, 5) THEN 1 
                WHEN rproject = 6 THEN 3 
                ELSE 2 
            END ASC")
            ->orderBy('id', 'desc');
        } elseif ($selectedSort === 'name_asc') {
            $query->orderBy('project_name', 'asc');
        } elseif ($selectedSort === 'oldest') {
            $query->orderBy('id', 'asc');
        } else {
            // latest
            $query->orderBy('id', 'desc');
        }

        $projects = $query->get();

        // Ambil daftar step dinamis dari rproject
        $rprojects = Rproject::orderBy('id', 'asc')->get();

        // Ambil daftar direktorat untuk filter
        $direktorats = Rdirektorat::orderBy('deskripsi', 'asc')->get();

        // Ambil list tahun unik dari tanggal_akhir & tanggal
        $tahunAkhirList = Project::selectRaw('YEAR(tanggal_akhir) as year')
            ->whereNotNull('tanggal_akhir')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $tahunTanggalList = Project::selectRaw('YEAR(tanggal) as year')
            ->whereNotNull('tanggal')
            ->distinct()
            ->pluck('year')
            ->toArray();

        $tahunList = array_unique(array_filter(array_merge([$currentYear], $tahunAkhirList, $tahunTanggalList)));
        rsort($tahunList);

        // Ringkasan KPI dihitung berdasarkan query dasar tahun, direktorat, dan keyword agar konsisten
        $baseMetricsQuery = Project::query();
        if (!empty($selectedTahun) && $selectedTahun !== 'all') {
            $baseMetricsQuery->where(function($q) use ($selectedTahun) {
                $q->whereYear('tanggal_akhir', $selectedTahun)
                  ->orWhere(function($sub) use ($selectedTahun) {
                      $sub->whereNull('tanggal_akhir')
                          ->whereYear('tanggal', $selectedTahun);
                  });
            });
        }
        if (!empty($selectedDirektorat)) {
            $baseMetricsQuery->where('direktorat', $selectedDirektorat);
        }
        if (!empty($searchKeyword)) {
            $baseMetricsQuery->where(function ($q) use ($searchKeyword) {
                $q->where('project_name', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('no_surat', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('pic_name', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('bagian', 'like', '%' . $searchKeyword . '%');
            });
        }

        $totalProjects = (clone $baseMetricsQuery)->count();
        $devProjectsCount = (clone $baseMetricsQuery)->where('rproject', 1)->count();
        $otherProgressProjectsCount = (clone $baseMetricsQuery)->whereIn('rproject', [2, 3, 4, 5])->count();
        $inProgressProjects = $devProjectsCount + $otherProgressProjectsCount;
        $completedProjects = (clone $baseMetricsQuery)->where('rproject', 6)->count();

        return view('kanban.index', compact(
            'projects',
            'rprojects',
            'direktorats',
            'tahunList',
            'selectedTahun',
            'selectedDirektorat',
            'selectedStatus',
            'selectedSort',
            'searchKeyword',
            'totalProjects',
            'devProjectsCount',
            'otherProgressProjectsCount',
            'inProgressProjects',
            'completedProjects'
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
