<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rproject extends Model
{
    // Nama tabel di database
    protected $table = 'rproject';

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
        'deskripsi',
        'icon',
        'status',
    ];
}
