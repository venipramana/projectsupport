<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Hproject extends Model
{
    use HasFactory;

    protected $table = 'hproject';

    public $timestamps = false;

    protected $fillable = [
        'idproject',
        'rproject',
        'tanggal',
        'catatan',
        'progress'
    ];

    protected static function booted()
    {
        static::saved(function ($hproject) {
            static::syncProjectStatus($hproject->idproject);
        });

        static::deleted(function ($hproject) {
            static::syncProjectStatus($hproject->idproject);
        });
    }

    /**
     * Synchronize parent Project table's rproject and tgl_update
     * with the latest record from hproject (ordered by tanggal DESC, id DESC).
     */
    public static function syncProjectStatus($projectId)
    {
        if (!$projectId) {
            return;
        }

        $latest = static::where('idproject', $projectId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $project = Project::find($projectId);
        if ($project && $latest) {
            $tglUpdate = $latest->tanggal ? Carbon::parse($latest->tanggal)->format('Y-m-d') : date('Y-m-d');
            $project->update([
                'rproject' => $latest->rproject,
                'tgl_update' => $tglUpdate,
            ]);
        }
    }

    /**
     * Synchronize all projects that have hproject records.
     */
    public static function syncAllProjects()
    {
        $projectIds = static::distinct()->pluck('idproject');
        foreach ($projectIds as $id) {
            static::syncProjectStatus($id);
        }
    }

    public function project_rel()
    {
        return $this->belongsTo(Project::class, 'idproject', 'id');
    }

    public function rproject_rel()
    {
        return $this->belongsTo(Rproject::class, 'rproject', 'id');
    }
}
