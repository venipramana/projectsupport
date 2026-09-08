<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rcatalog extends Model
{
    // Nama tabel di database
    protected $table = 'rcatalog';

    // Primary key dari tabel
    protected $primaryKey = 'id';

    // Disable timestamps if not present
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'current_version',
        'last_update',
        'id_direktorat',
        'url_base',
    ];

    /**
     * Relasi ke tabel rdirektorat.
     */
    public function direktorat()
    {
        return $this->belongsTo(Rdirektorat::class, 'id_direktorat', 'id');
    }

    /**
     * Relasi ke tabel project.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'id_catalog', 'id');
    }
}
