<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPanen extends Model {
    protected $fillable = [
        'provinsi_id',
        'user_id',
        'komoditas',
        'tanggal_panen',
        'luas_lahan_hektar',
        'jumlah_panen_ton',
        'catatan'
    ];

    public function provinsi(): BelongsTo {
        return $this->belongsTo(Provinsi::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
