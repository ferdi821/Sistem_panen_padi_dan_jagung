<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\CatatanPanen;
use App\Models\PengujianMutuBenih;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengujianMutuController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PengujianMutuBenih::with(['catatanPanen.provinsi', 'benih', 'user']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->whereHas('catatanPanen', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        $pengujians = $query->latest()->paginate(10);
        return view('pengujian.index', compact('pengujians'));
    }

    public function create()
    {
        $user = auth()->user();
        $panenQuery = CatatanPanen::with('provinsi');

        if ($user->hasRole('petugas_provinsi')) {
            $panenQuery->where('provinsi_id', $user->provinsi_id);
        }

        $catatanPanens = $panenQuery->latest()->get();
        $benihs = Benih::all();

        return view('pengujian.create', compact('catatanPanens', 'benihs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'catatan_panen_id'  => 'required|exists:catatan_panens,id',
            'benih_id'          => 'required|exists:benihs,id',
            'tanggal_uji'       => 'required|date',
            'volume_sampel_kg'  => 'required|numeric|min:0.1',
            'kadar_air_persen'  => 'required|numeric|min:0|max:100',
            'daya_tumbuh_persen'=> 'required|numeric|min:0|max:100',
            'jumlah_lolos_kg'   => 'required|numeric|min:0',
            'status_kelayakan'  => 'required|in:proses,layak,tidak_layak',
            'catatan_pengujian' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user = auth()->user();
            $validated['user_id'] = $user->id;

            $pengujian = PengujianMutuBenih::create($validated);
            $panen = CatatanPanen::findOrFail($validated['catatan_panen_id']);

            // Jika status LAYAK -> Tambahkan ke Stok Benih secara otomatis (DB Transaction)
            if ($validated['status_kelayakan'] === 'layak' && $validated['jumlah_lolos_kg'] > 0) {
                $stok = StokBenih::firstOrCreate(
                    [
                        'provinsi_id' => $panen->provinsi_id,
                        'benih_id'    => $validated['benih_id'],
                    ],
                    ['jumlah_stok_kg' => 0]
                );

                $stokSebelum = $stok->jumlah_stok_kg;
                $stokSesudah = $stokSebelum + $validated['jumlah_lolos_kg'];

                $stok->update(['jumlah_stok_kg' => $stokSesudah]);

                // Catat mutasi transaksi stok masuk
                TransaksiStokBenih::create([
                    'stok_benih_id'     => $stok->id,
                    'pengujian_mutu_id' => $pengujian->id,
                    'user_id'           => $user->id,
                    'jenis_transaksi'   => 'masuk',
                    'sumber_tujuan'     => 'Hasil Konversi Panen #' . $panen->id,
                    'jumlah_kg'         => $validated['jumlah_lolos_kg'],
                    'stok_sebelum'      => $stokSebelum,
                    'stok_sesudah'      => $stokSesudah,
                    'tanggal_transaksi' => $validated['tanggal_uji'],
                    'keterangan'        => 'Otomatis dari pengujian mutu panen layak.',
                ]);
            }
        });

        return redirect()->route('pengujian.index')->with('success', 'Data Pengujian Mutu berhasil disimpan!');
    }
}