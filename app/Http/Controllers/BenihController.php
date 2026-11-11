<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\StokBenih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BenihController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Benih::query();

        // Filter benih khusus provinsi user atau benih umum (provinsi_id null)
        if ($user->hasRole('petugas_provinsi')) {
            $query->where(function($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id)
                  ->orWhereNull('provinsi_id');
            });
        }

        $benihs = $query->latest()->paginate(10);

        return view('benih.index', compact('benihs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi menggunakan stok_minimum
        $request->validate([
            'kode_benih'    => 'required|string|max:50|unique:benihs,kode_benih',
            'nama_benih'    => 'required|string|max:255',
            'komoditas'     => 'required|string',
            'varietas'      => 'required|string|max:255',
            'kelas_benih'   => 'required|string|max:50',
            'stok_minimum'  => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $user) {
            $provinsiId = $user->hasRole('petugas_provinsi') ? $user->provinsi_id : null;

            // 1. Simpan Master Benih
            $benih = Benih::create([
                'kode_benih'   => $request->kode_benih,
                'nama_benih'   => $request->nama_benih,
                'komoditas'    => $request->komoditas,
                'varietas'     => $request->varietas,
                'kelas_benih'  => $request->kelas_benih,
                'stok_minimum' => $request->stok_minimum,
                'provinsi_id'  => $provinsiId,
            ]);

            // 2. Otomatis buatkan entry di Stok Benih Wilayah
            if ($provinsiId) {
                StokBenih::firstOrCreate(
                    [
                        'provinsi_id' => $provinsiId,
                        'benih_id'    => $benih->id,
                    ],
                    [
                        'jumlah_stok_kg' => 0.00
                    ]
                );
            }

            return redirect()->route('benih.index')->with('success', 'Master Data Benih & Stok Wilayah berhasil ditambahkan!');
        });
    }

    public function update(Request $request, $id)
    {
        $benih = Benih::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi') && $benih->provinsi_id && $benih->provinsi_id != $user->provinsi_id) {
            return back()->with('error', 'Akses ditolak! Anda tidak berhak mengedit benih provinsi lain.');
        }

        $request->validate([
            'kode_benih'    => 'required|string|max:50|unique:benihs,kode_benih,' . $id,
            'nama_benih'    => 'required|string|max:255',
            'komoditas'     => 'required|string',
            'varietas'      => 'required|string|max:255',
            'kelas_benih'   => 'required|string|max:50',
            'stok_minimum'  => 'required|numeric|min:0',
        ]);

        $benih->update([
            'kode_benih'   => $request->kode_benih,
            'nama_benih'   => $request->nama_benih,
            'komoditas'    => $request->komoditas,
            'varietas'     => $request->varietas,
            'kelas_benih'  => $request->kelas_benih,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()->route('benih.index')->with('success', 'Master Data Benih berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $benih = Benih::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi') && $benih->provinsi_id && $benih->provinsi_id != $user->provinsi_id) {
            return back()->with('error', 'Akses ditolak!');
        }

        StokBenih::where('benih_id', $benih->id)->delete();
        $benih->delete();

        return redirect()->route('benih.index')->with('success', 'Master Data Benih berhasil dihapus!');
    }
}