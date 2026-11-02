<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benih extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pengujianMutus()
    {
        return $this->hasMany(PengujianMutuBenih::class, 'benih_id');
    }

    public function stokBenihs()
    {
        return $this->hasMany(StokBenih::class, 'benih_id');
    }
}