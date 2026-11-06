<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanPanen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengujianMutus()
    {
        return $this->hasMany(PengujianMutuBenih::class, 'catatan_panen_id');
    }

    public function benih()
    {
        return $this->belongsTo(Benih::class, 'benih_id');
    }
}