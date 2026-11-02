<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokBenihController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = StokBenih::with(['provinsi', 'benih']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        $stokList = $query->latest()->paginate(10);
        return view('stok.index', compact('stokList'));
    }

    public function createKeluar()
    {
        $user = auth()->user();
        $stokQuery = StokBenih::with(['provinsi', 'benih'])->where('jumlah_stok_kg', '>', 0);

        if ($user->hasRole('petugas_provinsi')) {
            $stokQuery->where('provinsi_id', $user->provinsi_id);
        }

        $stokOptions = $stokQuery->get();
        return view('stok.keluar', compact('stokOptions'));
    }

    public function storeKeluar(Request $request)
    {
        $validated = $request->validate([
            'stok_benih_id'     => 'required|exists:stok_benihs,id',
            'sumber_tujuan'     => 'required|string|max:150',
            'jumlah_kg'         => 'required|numeric|min:0.01',
            'tanggal_transaksi' => 'required|date',
            'keterangan'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $stok = StokBenih::lockForUpdate()->findOrFail($validated['stok_benih_id']);

            // Validasi keamanan: Stok tidak boleh bernilai negatif
            if ($stok->jumlah_stok_kg < $validated['jumlah_kg']) {
                return back()->withInput()->with('error', 'Gagal! Stok tersedia (' . number_format($stok->jumlah_stok_kg, 2) . ' kg) tidak mencukupi untuk dikeluarkan sebesar ' . number_format($validated['jumlah_kg'], 2) . ' kg.');
            }

            $stokSebelum = $stok->jumlah_stok_kg;
            $stokSesudah = $stokSebelum - $validated['jumlah_kg'];

            $stok->update(['jumlah_stok_kg' => $stokSesudah]);

            TransaksiStokBenih::create([
                'stok_benih_id'     => $stok->id,
                'pengujian_mutu_id' => null,
                'user_id'           => auth()->id(),
                'jenis_transaksi'   => 'keluar',
                'sumber_tujuan'     => $validated['sumber_tujuan'],
                'jumlah_kg'         => $validated['jumlah_kg'],
                'stok_sebelum'      => $stokSebelum,
                'stok_sesudah'      => $stokSesudah,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'keterangan'        => $validated['keterangan'],
            ]);

            return redirect()->route('stok.index')->with('success', 'Transaksi stok keluar berhasil diproses!');
        });
    }

    public function riwayatMutasi()
    {
        $user = auth()->user();
        $query = TransaksiStokBenih::with(['stokBenih.provinsi', 'stokBenih.benih', 'user']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->whereHas('stokBenih', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        $mutasis = $query->latest()->paginate(15);
        return view('stok.riwayat', compact('mutasis'));
    }
}