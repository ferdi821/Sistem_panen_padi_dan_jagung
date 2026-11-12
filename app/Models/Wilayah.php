<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'kabupatens';

    protected $fillable = [
        'kode_wilayah',
        'kode_kabupaten',
        'provinsi_id',
        'nama_kabupaten',
        'nama_gudang',
        'alamat',
        'kapasitas_ton',
        'is_active',
    ];

    /**
     * Accessor agar $item->kabupaten di view tetap terbaca dari kolom nama_kabupaten
     */
    public function getKabupatenAttribute()
    {
        return $this->attributes['nama_kabupaten'] ?? $this->attributes['kabupaten'] ?? '-';
    }

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}