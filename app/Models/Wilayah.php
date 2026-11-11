<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wilayah extends Model
{
    use HasFactory;

    // Diubah ke 'kabupatens' sesuai tabel riil di database MySQL kamu
    protected $table = 'kabupatens';

    protected $fillable = [
        'kode_wilayah',
        'provinsi_id',
        'kabupaten',
        'nama_gudang',
        'alamat',
        'kapasitas_ton',
        'is_active',
    ];

    /**
     * Relasi ke model Provinsi untuk mengatasi RelationNotFoundException
     */
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}