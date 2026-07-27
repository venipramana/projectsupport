<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    // Nama tabel di database
    protected $table = 'pengguna';

    // Primary key dari tabel
    protected $primaryKey = 'idtable';

    // Karena tidak ada kolom created_at dan updated_at
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idpengguna',
        'password',
        'nama',
        'email',
        'kodelokasi',
        'levelpengguna',
        'status',
        'bypass',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Laravel akan menggunakan field ini untuk menyimpan token remember me.
     * Karena tabel 'pengguna' tidak memiliki kolom 'remember_token', kita
     * mengembalikan nama kolom kosong agar Laravel tidak mencoba
     * memperbarui kolom yang tidak ada saat "remember me" digunakan.
     */
    public function getRememberTokenName()
    {
        return null; // Menonaktifkan remember_token
    }
}
