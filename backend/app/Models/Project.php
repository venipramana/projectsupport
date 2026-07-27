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
        'vendorname', 'rkap', 'bsurkap'
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
}
