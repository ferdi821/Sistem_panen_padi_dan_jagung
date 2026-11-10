<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\Provinsi;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokBenihController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $allBenih = Benih::all();
            foreach ($allBenih as $benih) {
                StokBenih::firstOrCreate(
                    [
                        'provinsi_id' => $user->provinsi_id,
                        'benih_id'    => $benih->id,
                    ],
                    [
                        'jumlah_stok_kg' => 0.00
                    ]
                );
            }
        }

        $query = StokBenih::with(['provinsi', 'benih']);
        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }
        $stokList = $query->latest()->paginate(5);

        $stokQuery = StokBenih::with(['provinsi', 'benih']);
        if ($user->hasRole('petugas_provinsi')) {
            $stokQuery->where('provinsi_id', $user->provinsi_id);
        }
        $stokOptions = $stokQuery->get();

        $provinsis = Provinsi::all();
        $benihs    = Benih::all();

        return view('stok.index', compact('stokList', 'stokOptions', 'provinsis', 'benihs'));
    }

    public function edit($id)
    {
        $stok = StokBenih::findOrFail($id);
        $provinsis = Provinsi::all();
        $benihs = Benih::all();

        return view('stok.edit', compact('stok', 'provinsis', 'benihs'));
    }

    public function update(Request $request, $id)
    {
        $stok = StokBenih::findOrFail($id);
        $user = auth()->user();

        if ($user->hasRole('petugas_provinsi') && $stok->provinsi_id != $user->provinsi_id) {
            return back()->with('error', 'Akses ditolak! Anda hanya berhak mengedit data stok wilayah provinsi Anda.');
        }

        $validated = $request->validate([
            'provinsi_id'    => 'required|exists:provinsis,id',
            'benih_id'       => 'required|exists:benihs,id',
            'jumlah_stok_kg' => 'required|numeric|min:0',
        ]);

        if ($user->hasRole('petugas_provinsi')) {
            $validated['provinsi_id'] = $user->provinsi_id;
        }

        $stok->update($validated);

        return redirect()->route('stok.index')->with('success', 'Data persediaan stok benih berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $stok = StokBenih::findOrFail($id);

        $adaTransaksi = TransaksiStokBenih::where('stok_benih_id', $id)->exists();
        if ($adaTransaksi) {
            return back()->with('error', 'Gagal menghapus! Data stok ini sudah memiliki riwayat transaksi mutasi.');
        }

        $stok->delete();

        return redirect()->route('stok.index')->with('success', 'Data persediaan stok benih berhasil dihapus!');
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

            if ($stok->jumlah_stok_kg < $validated['jumlah_kg']) {
                return back()->withInput()->with('error', 'Gagal! Stok tersedia (' . number_format($stok->jumlah_stok_kg, 2) . ' Ton) tidak mencukupi.');
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

    public function storeMasuk(Request $request)
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

            $stokSebelum = $stok->jumlah_stok_kg;
            $stokSesudah = $stokSebelum + $validated['jumlah_kg'];

            $stok->update(['jumlah_stok_kg' => $stokSesudah]);

            TransaksiStokBenih::create([
                'stok_benih_id'     => $stok->id,
                'pengujian_mutu_id' => null,
                'user_id'           => auth()->id(),
                'jenis_transaksi'   => 'masuk',
                'sumber_tujuan'     => $validated['sumber_tujuan'],
                'jumlah_kg'         => $validated['jumlah_kg'],
                'stok_sebelum'      => $stokSebelum,
                'stok_sesudah'      => $stokSesudah,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'keterangan'        => $validated['keterangan'],
            ]);

            return redirect()->route('stok.index')->with('success', 'Transaksi stok masuk berhasil diproses dan stok bertambah!');
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

    public function editRiwayat($id)
    {
        $transaksi = TransaksiStokBenih::with(['stokBenih.provinsi', 'stokBenih.benih'])->findOrFail($id);
        return view('stok.edit_riwayat', compact('transaksi'));
    }

    public function updateRiwayat(Request $request, $id)
    {
        $validated = $request->validate([
            'jumlah_kg'         => 'required|numeric|min:0.01',
            'sumber_tujuan'     => 'required|string|max:150',
            'tanggal_transaksi' => 'required|date',
            'keterangan'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $id) {
            $transaksi = TransaksiStokBenih::findOrFail($id);
            $stok = StokBenih::lockForUpdate()->findOrFail($transaksi->stok_benih_id);

            $selisih = $validated['jumlah_kg'] - $transaksi->jumlah_kg;

            if ($transaksi->jenis_transaksi === 'masuk') {
                if ($selisih < 0 && $stok->jumlah_stok_kg < abs($selisih)) {
                    return back()->with('error', 'Gagal mengubah! Stok tersisa tidak mencukupi.');
                }
                $stok->jumlah_stok_kg += $selisih;
            } elseif ($transaksi->jenis_transaksi === 'keluar') {
                if ($selisih > 0 && $stok->jumlah_stok_kg < $selisih) {
                    return back()->with('error', 'Gagal mengubah! Stok tersisa tidak mencukupi.');
                }
                $stok->jumlah_stok_kg -= $selisih;
            }

            $stok->save();

            $transaksi->update([
                'jumlah_kg'         => $validated['jumlah_kg'],
                'stok_sesudah'      => $stok->jumlah_stok_kg,
                'sumber_tujuan'     => $validated['sumber_tujuan'],
                'tanggal_transaksi' => \Carbon\Carbon::parse($validated['tanggal_transaksi']),
                'keterangan'        => $validated['keterangan'],
            ]);

            return redirect()->route('stok.riwayat')->with('success', 'Data transaksi mutasi berhasil diperbarui!');
        });
    }

    public function destroyRiwayat($id)
    {
        return DB::transaction(function () use ($id) {
            $transaksi = TransaksiStokBenih::findOrFail($id);
            $stok = StokBenih::lockForUpdate()->find($transaksi->stok_benih_id);

            if ($stok) {
                if ($transaksi->jenis_transaksi === 'masuk') {
                    if ($stok->jumlah_stok_kg < $transaksi->jumlah_kg) {
                        return back()->with('error', 'Gagal menghapus! Stok saat ini lebih kecil dari transaksi.');
                    }
                    $stok->decrement('jumlah_stok_kg', $transaksi->jumlah_kg);
                } elseif ($transaksi->jenis_transaksi === 'keluar') {
                    $stok->increment('jumlah_stok_kg', $transaksi->jumlah_kg);
                }
            }

            $transaksi->delete();

            return redirect()->route('stok.riwayat')->with('success', 'Riwayat transaksi mutasi berhasil dihapus!');
        });
    }
}