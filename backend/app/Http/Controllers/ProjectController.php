<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Hproject;
use App\Models\Rdirektorat;
use App\Models\Rproject;
use App\Models\Pengguna;
use App\Models\Rvulnerability;
use App\Models\Rrkap;
use App\Models\Rcatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with([
            'direktorat_rel', 
            'rproject_relation', 
            'leadby_rel', 
            'supportby_rel', 
            'vul_passed_rel', 
            'rkap_rel', 
            'catalog_rel'
        ])->orderBy('id', 'desc');

        // Search by project_name
        if ($request->has('search') && $request->search != '') {
            $query->where('project_name', 'like', '%' . $request->search . '%')
                  ->orWhere('no_surat', 'like', '%' . $request->search . '%');
        }

        // Filter Direktorat
        if ($request->has('filter_direktorat') && $request->filter_direktorat != '') {
            $query->where('direktorat', $request->filter_direktorat);
        }

        // Filter Status Project (rproject)
        if ($request->has('filter_status') && $request->filter_status != '') {
            $query->where('rproject', $request->filter_status);
        }

        // Filter Tahun
        if ($request->has('filter_tahun') && $request->filter_tahun != '') {
            $query->whereYear('tanggal', $request->filter_tahun);
        }

        $projects = $query->paginate(25)->withQueryString();

        // Get data for dropdowns
        $direktorats = Rdirektorat::all();
        $rprojects = Rproject::all();
        $penggunas = Pengguna::where('status', 1)->orderBy('nama', 'asc')->get();
        $vulnerabilities = Rvulnerability::all();
        $rrkaps = Rrkap::all();

        // Get max catalog_version per id_catalog from project table using version_compare
        $projectCatalogVersions = Project::whereNotNull('id_catalog')
            ->whereNotNull('catalog_version')
            ->where('catalog_version', '!=', '')
            ->where('catalog_version', '!=', '-')
            ->get()
            ->groupBy('id_catalog');

        $maxVersions = [];
        foreach ($projectCatalogVersions as $idCatalog => $group) {
            $versions = $group->pluck('catalog_version')->unique()->toArray();
            usort($versions, 'version_compare');
            $maxVersions[$idCatalog] = end($versions);
        }

        $rcatalogs = Rcatalog::orderBy('id', 'asc')->get();
        foreach ($rcatalogs as $cat) {
            $cat->max_version = $maxVersions[$cat->id] ?? $cat->current_version ?? '-';
        }

        // Extract distinct years from 'tanggal' field
        $tahunList = Project::selectRaw('YEAR(tanggal) as year')
            ->whereNotNull('tanggal')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedDirektorat = $request->filter_direktorat ?? '';
        $selectedStatus = $request->filter_status ?? '';
        $selectedTahun = $request->filter_tahun ?? '';
        $searchKeyword = $request->search ?? '';

        return view('project.index', compact(
            'projects', 'direktorats', 'rprojects', 'penggunas', 
            'vulnerabilities', 'rrkaps', 'rcatalogs', 'tahunList',
            'selectedDirektorat', 'selectedStatus', 'selectedTahun', 'searchKeyword'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalog_version' => 'required',
        ], [
            'catalog_version.required' => 'Catalog Version mandatory harus diisi.'
        ]);

        $data = $request->all();

        // Convert dates from dd-mm-yyyy to Y-m-d
        $dateFields = ['tanggal', 'tanggal_awal', 'tanggal_akhir', 'tgl_update'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                // Assuming input is exactly dd-mm-yyyy from flatpickr or JS
                $data[$field] = \Carbon\Carbon::createFromFormat('d-m-Y', $data[$field])->format('Y-m-d');
            } else {
                if ($field === 'tanggal') {
                    $data['tanggal'] = date('Y-m-d'); // Default current date
                } else {
                    $data[$field] = null;
                }
            }
        }

        // Generate Token
        $data['token'] = strtoupper(Str::random(6));

        // Clean bsurkap (remove dots and commas)
        if (!empty($data['bsurkap'])) {
            $data['bsurkap'] = preg_replace('/[.,]/', '', $data['bsurkap']);
        }

        // Status Development ID (default 1)
        // Catatan: rproject = 0 berarti "ONHOLD", jadi 0 tidak boleh dianggap kosong
        if (!isset($data['rproject']) || $data['rproject'] === '' || $data['rproject'] === null) {
            $data['rproject'] = 1;
        }

        $project = Project::create($data);

        // Otomatis buat folder di bucket MinIO dan hubungkan atribut folder_evidence
        $folderName = "evidence_project_" . $project->id;
        try {
            \Illuminate\Support\Facades\Storage::disk('minio')->put("{$folderName}/.keep", "");
            $project->folder_evidence = $folderName;
            $project->save();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("MinIO error during project creation: " . $e->getMessage());
        }

        // Otomatis insert 1 row ke tabel hproject dengan status Development
        Hproject::create([
            'idproject' => $project->id,
            'rproject'  => $project->rproject ?? 1,
            'tanggal'   => $project->tanggal ?? date('Y-m-d'),
            'catatan'   => !empty($project->catatan) ? substr($project->catatan, 0, 150) : 'Development',
            'progress'  => 0,
        ]);

        return redirect()->route('project.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'catalog_version' => 'required',
        ], [
            'catalog_version.required' => 'Catalog Version mandatory harus diisi.'
        ]);

        $project = Project::findOrFail($id);
        $data = $request->all();

        // Convert dates from dd-mm-yyyy to Y-m-d
        $dateFields = ['tanggal', 'tanggal_awal', 'tanggal_akhir', 'tgl_update'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = \Carbon\Carbon::createFromFormat('d-m-Y', $data[$field])->format('Y-m-d');
            } else {
                $data[$field] = null;
            }
        }

        // Clean bsurkap
        if (!empty($data['bsurkap'])) {
            $data['bsurkap'] = preg_replace('/[.,]/', '', $data['bsurkap']);
        }

        $project->update($data);

        return redirect()->route('project.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('project.index')->with('success', 'Project berhasil dihapus.');
    }

    /**
     * Quick status update triggered from Donut Context Menu.
     * Updates project table (rproject field) and inserts a record into hproject table.
     */
    public function quickStatusUpdate(Request $request)
    {
        $request->validate([
            'idproject' => 'required|integer|exists:project,id',
            'rproject' => 'required|integer|exists:rproject,id',
            'catatan' => 'nullable|string|max:150',
        ]);

        $project = Project::with('rproject_relation')->findOrFail($request->idproject);

        // Authorization check: if user is level 3 (developer), only allow if they are the leadby
        if (auth()->check() && auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk mengubah status project ini.'
            ], 403);
        }

        $targetStatus = Rproject::findOrFail($request->rproject);

        // Get latest progress from previous hproject record if available
        $latestHproject = Hproject::where('idproject', $project->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        $currentProgress = $latestHproject ? ($latestHproject->progress ?? 0) : 0;

        $catatan = $request->catatan ?: ('Perubahan status ke ' . $targetStatus->deskripsi);
        $tanggalHariIni = now()->format('Y-m-d');

        // 1. Simpan historis ke tabel hproject
        $hproject = Hproject::create([
            'idproject' => $project->id,
            'rproject' => $targetStatus->id,
            'tanggal' => $tanggalHariIni,
            'catatan' => $catatan,
            'progress' => $currentProgress,
        ]);

        // 2. Update status dan tgl_update di tabel project
        $project->update([
            'rproject' => $targetStatus->id,
            'tgl_update' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status project \"{$project->project_name}\" berhasil diubah menjadi \"{$targetStatus->deskripsi}\".",
            'data' => [
                'idproject' => $project->id,
                'project_name' => $project->project_name,
                'rproject' => $targetStatus->id,
                'status_name' => $targetStatus->deskripsi,
                'status_icon' => $targetStatus->icon,
                'icon_url' => asset('icons/' . $targetStatus->icon),
                'tgl_update' => $project->tgl_update ? date('d-m-Y H:i', strtotime($project->tgl_update)) : date('d-m-Y H:i'),
                'hproject_id' => $hproject->id,
            ]
        ]);
    }
}
