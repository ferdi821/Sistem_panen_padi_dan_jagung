<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'provinsi_id'];
    protected $hidden = ['password', 'remember_token'];

    public function provinsi(): BelongsTo {
        return $this->belongsTo(Provinsi::class);
    }

    public function catatanPanens(): HasMany {
        return $this->hasMany(CatatanPanen::class);
    }

    // Tambahan relasi aktivitas pengujian & transaksi
    public function pengujianMutus(): HasMany {
        return $this->hasMany(PengujianMutuBenih::class);
    }

    public function transaksiStoks(): HasMany {
        return $this->hasMany(TransaksiStokBenih::class);
    }
}