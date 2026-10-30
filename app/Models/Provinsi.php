<?php

namespace App\Models;

use App\Models\CatatanPanen;
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
}
