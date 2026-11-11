<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benih extends Model
{
    use HasFactory;

    protected $fillable = [
        'provinsi_id',
        'kode_benih',
        'nama_benih',
        'komoditas',
        'varietas',
        'kelas_benih',
        'stok_minimum',
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}