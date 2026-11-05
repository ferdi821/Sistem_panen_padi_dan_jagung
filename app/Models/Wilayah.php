<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_wilayah',
        'nama_provinsi',
        'nama_kabupaten',
        'nama_gudang',
        'alamat_gudang',
        'kapasitas_ton',
        'is_active',
    ];
}