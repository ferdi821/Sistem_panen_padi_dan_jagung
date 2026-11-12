<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\Provinsi;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Carbon\Carbon;
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

        $stokQuery = StokBenih::with(['provinsi', 'benih']);
        if ($user->hasRole('petugas_provinsi')) {
            $stokQuery->where('provinsi_id', $user->provinsi_id);
        }
        $stokOptions = $stokQuery->get();

        $provinsis = Provinsi::all();

        $benihQuery = Benih::query();
        if ($user->hasRole('petugas_provinsi')) {
            $benihQuery->where(function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id)
                  ->orWhereNull('provinsi_id');
            });
        }
        $benihs = $benihQuery->get();

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
            return back()->with('error', 'Akses ditolak!');
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
            return back()->with('error', 'Gagal menghapus! Stok ini sudah memiliki riwayat transaksi.');
        }

        $stok->delete();

        return redirect()->route('stok.index')->with('success', 'Data persediaan stok benih berhasil dihapus!');
    }

    // TRANSAKSI STOK MASUK
    public function storeMasuk(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'benih_id'          => 'required|exists:benihs,id',
            'sumber_tujuan'     => 'required|string|max:150',
            'jumlah_kg'         => 'required|numeric|min:0.01',
            'tanggal_transaksi' => 'required|date',
            'keterangan'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $provinsiId = $user->provinsi_id;

            $stok = StokBenih::firstOrCreate(
                [
                    'provinsi_id' => $provinsiId,
                    'benih_id'    => $validated['benih_id'],
                ],
                [
                    'jumlah_stok_kg' => 0.00,
                ]
            );

            $stokSebelum = $stok->jumlah_stok_kg;
            $stokSesudah = $stokSebelum + $validated['jumlah_kg'];

            $stok->update(['jumlah_stok_kg' => $stokSesudah]);

            TransaksiStokBenih::create([
                'stok_benih_id'     => $stok->id,
                'pengujian_mutu_id' => null,
                'user_id'           => $user->id,
                'jenis_transaksi'   => 'masuk',
                'sumber_tujuan'     => $validated['sumber_tujuan'],
                'jumlah_kg'         => $validated['jumlah_kg'],
                'stok_sebelum'      => $stokSebelum,
                'stok_sesudah'      => $stokSesudah,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'keterangan'        => $validated['keterangan'],
            ]);

            return redirect()->route('stok.index')->with('success', 'Stok masuk sebanyak ' . number_format($validated['jumlah_kg'], 2) . ' Ton berhasil diproses!');
        });
    }

    // TRANSAKSI STOK KELUAR
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

            return redirect()->route('stok.index')->with('success', 'Stok keluar sebanyak ' . number_format($validated['jumlah_kg'], 2) . ' Ton berhasil diproses!');
        });
    }

    public function riwayatMutasi(Request $request)
    {
        $user = auth()->user();
        $query = TransaksiStokBenih::with(['stokBenih.provinsi', 'stokBenih.benih', 'user']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->whereHas('stokBenih', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        $tanggal = $request->get('tanggal');

        if ($request->filled('tanggal')) {
            // Jika memilih tanggal spesifik (termasuk minggu lalu)
            $query->whereDate('tanggal_transaksi', Carbon::parse($tanggal)->toDateString());
        } else {
            // Default: otomatis tampilkan mutasi minggu berjalan saja (auto-reset mingguan)
            $query->whereBetween('tanggal_transaksi', [
                Carbon::now()->startOfWeek()->toDateString(),
                Carbon::now()->endOfWeek()->toDateString(),
            ]);
        }

        $mutasis = $query->orderBy('tanggal_transaksi', 'desc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stok.riwayat', compact('mutasis', 'tanggal'));
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
                'tanggal_transaksi' => Carbon::parse($validated['tanggal_transaksi']),
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