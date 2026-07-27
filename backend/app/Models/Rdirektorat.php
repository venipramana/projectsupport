<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rdirektorat extends Model
{
    // Nama tabel di database
    protected $table = 'rdirektorat';

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
        'status',
        'cluster',
    ];
}
