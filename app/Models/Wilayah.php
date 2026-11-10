<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayahs';

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