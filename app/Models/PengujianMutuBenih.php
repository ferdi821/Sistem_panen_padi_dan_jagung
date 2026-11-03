<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengujianMutuBenih extends Model
{
    use HasFactory;

    protected $fillable = [
        'catatan_panen_id',
        'benih_id',
        'user_id',
        'tanggal_uji',
        'volume_sampel_kg',
        'kadar_air_persen',
        'daya_tumbuh_persen',
        'jumlah_lolos_kg', // DIUBAH KE INI SUPAYA KLOP DENGAN DATABASE & CONTROLLER
        'status_kelayakan',
        'catatan_pengujian',
    ];

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