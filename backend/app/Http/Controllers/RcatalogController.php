<?php

namespace App\Http\Controllers;

use App\Models\Rcatalog;
use App\Models\Rdirektorat;
use App\Models\Project;
use Illuminate\Http\Request;

class RcatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Rcatalog::with('direktorat')->orderBy('description', 'asc');

        if ($request->has('filter_direktorat') && $request->filter_direktorat != '') {
            $query->where('id_direktorat', $request->filter_direktorat);
        }

        $rcatalogs = $query->get();
        // Ambil data direktorat untuk opsi dropdown di modal create/edit dan filter
        $direktorats = Rdirektorat::all();
        
        $selectedDirektorat = $request->filter_direktorat ?? '';

        // Summary catalog per-direktorat untuk Donut Chart
        $catalogs_by_direktorat = Rcatalog::with('direktorat')
            ->selectRaw('id_direktorat, count(*) as total')
            ->groupBy('id_direktorat')
            ->get()
            ->map(function ($item) {
                return [
                    'id_direktorat' => $item->id_direktorat,
                    'direktorat' => $item->direktorat ? $item->direktorat->deskripsi : 'Lainnya',
                    'total' => (int) $item->total,
                ];
            });

        return view('rcatalog.index', compact('rcatalogs', 'direktorats', 'selectedDirektorat', 'catalogs_by_direktorat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'current_version' => 'nullable|string|max:10',
            'last_update' => 'nullable|string|max:10',
            'id_direktorat' => 'nullable|integer|exists:rdirektorat,id',
            'url_base' => 'nullable|string|max:255',
        ]);

        Rcatalog::create($request->all());

        return redirect()->route('rcatalog.index')->with('success', 'Katalog berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $rcatalog = Rcatalog::findOrFail($id);

        $request->validate([
            'description' => 'nullable|string|max:255',
            'current_version' => 'nullable|string|max:10',
            'last_update' => 'nullable|string|max:10',
            'id_direktorat' => 'nullable|integer|exists:rdirektorat,id',
            'url_base' => 'nullable|string|max:255',
        ]);

        $rcatalog->update($request->all());

        return redirect()->route('rcatalog.index')->with('success', 'Katalog berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $rcatalog = Rcatalog::findOrFail($id);
        $rcatalog->delete();

        return redirect()->route('rcatalog.index')->with('success', 'Katalog berhasil dihapus.');
    }

    public function getProjects($id)
    {
        $projects = Project::with('rproject_relation')
            ->where('id_catalog', $id)
            ->orderBy('catalog_version', 'desc')
            ->get();

        // Format dates
        $formattedProjects = $projects->map(function ($project) {
            $formattedDate = '-';
            if ($project->tanggal) {
                // Ensure date format is dd-mm-yyyy
                $formattedDate = date('d-m-Y', strtotime($project->tanggal));
            }

            return [
                'id' => $project->id,
                'project_name' => $project->project_name,
                'tanggal' => $formattedDate,
                'catalog_version' => $project->catalog_version,
                'rproject_deskripsi' => $project->rproject_relation ? $project->rproject_relation->deskripsi : '-',
            ];
        });

        return response()->json($formattedProjects);
    }
}
