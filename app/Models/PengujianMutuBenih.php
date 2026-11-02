<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengujianMutuBenih extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function catatanPanen()
    {
        return $this->belongsTo(CatatanPanen::class, 'catatan_panen_id');
    }

    public function benih()
    {
        return $this->belongsTo(Benih::class, 'benih_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksiStok()
    {
        return $this->hasOne(TransaksiStokBenih::class, 'pengujian_mutu_id');
    }
}