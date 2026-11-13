<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBenih extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function benih()
    {
        return $this->belongsTo(Benih::class, 'benih_id');
    }

    public function transaksiStoks()
    {
        return $this->hasMany(TransaksiStokBenih::class, 'stok_benih_id');
    }
}