<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nonproject extends Model
{
    // Nama tabel di database
    protected $table = 'nonproject';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps karena tabel tidak memiliki created_at & updated_at
    public $timestamps = false;

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'kegiatan',
        'lokasi',
        'catatan',
        'pic',
    ];
}
