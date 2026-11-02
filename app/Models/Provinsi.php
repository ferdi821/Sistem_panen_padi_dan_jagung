<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model {
    protected $fillable = ['kode_provinsi', 'nama_provinsi'];

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function catatanPanens(): HasMany {
        return $this->hasMany(CatatanPanen::class);
    }

    // Tambahan relasi ke stok benih wilayah
    public function stokBenihs(): HasMany {
        return $this->hasMany(StokBenih::class);
    }
}