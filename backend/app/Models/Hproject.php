<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function project_rel()
    {
        return $this->belongsTo(Project::class, 'idproject', 'id');
    }

    public function rproject_rel()
    {
        return $this->belongsTo(Rproject::class, 'rproject', 'id');
    }
}
