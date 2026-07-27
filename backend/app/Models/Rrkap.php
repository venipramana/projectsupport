<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rrkap extends Model
{
    // Nama tabel di database
    protected $table = 'rrkap';

    // Primary key dari tabel
    protected $primaryKey = 'idrkap';

    // Specify if the IDs are auto-incrementing. 
    // Since DESCRIBE didn't show auto_increment, we set this to false to allow manual entry if needed, 
    // or true if it's actually auto-incrementing. We'll leave it as true but make idrkap fillable just in case.
    public $incrementing = false;

    // Disable timestamps if not present
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idrkap',
        'tahun_rkap',
        'nama_rkap',
        'kode_rkap',
        'bsu_rkap',
    ];
}
