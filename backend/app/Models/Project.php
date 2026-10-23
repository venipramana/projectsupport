<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';
    protected $primaryKey = 'id';
    public $timestamps = false; // Based on schema, there are no created_at/updated_at but there is tgl_update. We'll handle it manually if needed.

    protected $fillable = [
        'direktorat', 'bagian', 'pic_name', 'no_surat', 'tanggal',
        'project_name', 'tanggal_awal', 'tanggal_akhir', 'catatan',
        'rproject', 'leadby', 'supportby', 'tgl_update', 'token',
        'vul_passed', 'id_catalog', 'catalog_version', 'byvendor',
        'vendorname', 'rkap', 'bsurkap', 'folder_evidence'
    ];

    /**
     * Relationship to rproject table.
     */
    public function rproject_relation()
    {
        return $this->belongsTo(Rproject::class, 'rproject', 'id');
    }

    public function direktorat_rel()
    {
        return $this->belongsTo(Rdirektorat::class, 'direktorat', 'id');
    }

    public function leadby_rel()
    {
        return $this->belongsTo(Pengguna::class, 'leadby', 'idpengguna');
    }

    public function supportby_rel()
    {
        return $this->belongsTo(Pengguna::class, 'supportby', 'idpengguna');
    }

    public function vul_passed_rel()
    {
        return $this->belongsTo(Rvulnerability::class, 'vul_passed', 'id');
    }

    public function rkap_rel()
    {
        return $this->belongsTo(Rrkap::class, 'rkap', 'nama_rkap');
    }

    public function catalog_rel()
    {
        return $this->belongsTo(Rcatalog::class, 'id_catalog', 'id');
    }

    public function hprojects()
    {
        return $this->hasMany(Hproject::class, 'idproject', 'id');
    }

    /**
     * Get latest date when this project had status = 1 (Development).
     */
    public function getLastDevDateAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->relationLoaded('hprojects')) {
            $latestH = $this->hprojects
                ->where('rproject', 1)
                ->sortByDesc('tanggal')
                ->first();
            if ($latestH && !empty($latestH->tanggal)) {
                return $latestH->tanggal;
            }
        }

        if ($this->rproject == 1) {
            $hdev = Hproject::where('idproject', $this->id)
                ->where('rproject', 1)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            if ($hdev && !empty($hdev->tanggal)) {
                return $hdev->tanggal;
            }
            return $this->tanggal_awal ?? $this->tanggal ?? ($this->tgl_update ? \Carbon\Carbon::parse($this->tgl_update)->format('Y-m-d') : null);
        }

        return null;
    }

    /**
     * Get development duration in days from latest development date to today (current_date).
     */
    public function getDevDurationDaysAttribute()
    {
        $startDateStr = $this->last_dev_date;
        if (!$startDateStr) {
            return null;
        }

        try {
            $startDate = \Carbon\Carbon::parse($startDateStr)->startOfDay();
            $today = \Carbon\Carbon::today();
            return $startDate->isFuture() ? 0 : (int) $startDate->diffInDays($today);
        } catch (\Exception $e) {
            return null;
        }
    }
}
