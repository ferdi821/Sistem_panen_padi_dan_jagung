<?php

namespace App\Exports;

use App\Models\CatatanPanen;
use Illuminate\Database\Eloquent\Builder; // Import Eloquent Builder
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PanenExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $provinsiId;
    protected $komoditas;

    public function __construct($provinsiId = null, $komoditas = null)
    {
        $this->provinsiId = $provinsiId;
        $this->komoditas = $komoditas;
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        $query = CatatanPanen::query()->with(['provinsi', 'user']);

        if ($this->provinsiId) {
            $query->where('provinsi_id', $this->provinsiId);
        }

        if ($this->komoditas) {
            $query->where('komoditas', $this->komoditas);
        }

        return $query->latest('tanggal_panen');
    }

    public function headings(): array
    {
        return [
            'ID Panen',
            'Tanggal Panen',
            'Provinsi',
            'Komoditas',
            'Luas Lahan (Ha)',
            'Jumlah Panen (Ton)',
            'Petugas Pencatat',
            'Catatan'
        ];
    }

    public function map($panen): array
    {
        return [
            $panen->id,
            $panen->tanggal_panen,
            $panen->provinsi->nama_provinsi ?? '-',
            strtoupper($panen->komoditas),
            $panen->luas_lahan_hektar,
            $panen->jumlah_panen_ton,
            $panen->user->name ?? '-',
            $panen->catatan ?? '-'
        ];
    }
}
