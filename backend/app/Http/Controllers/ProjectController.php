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
        if (empty($data['rproject'])) {
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
}
