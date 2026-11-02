<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiStokBenih extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stokBenih()
    {
        return $this->belongsTo(StokBenih::class, 'stok_benih_id');
    }

    public function pengujianMutu()
    {
        return $this->belongsTo(PengujianMutuBenih::class, 'pengujian_mutu_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}