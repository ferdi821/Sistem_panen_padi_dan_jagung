<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\Provinsi;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class StokBenihController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil daftar ID wilayah (kabupaten) yang memiliki stok secara unik dengan paginasi maksimal 5 kabupaten per halaman
        $wilayahQuery = StokBenih::select('wilayah_id')
            ->when($user->hasRole('petugas_provinsi'), function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            })
            ->whereNotNull('wilayah_id')
            ->distinct();

        $paginatedWilayahIds = $wilayahQuery->paginate(5);
        $activeWilayahIds = $paginatedWilayahIds->pluck('wilayah_id');

        // 2. Ambil data stok lengkap HANYA untuk kabupaten yang masuk di halaman aktif tersebut
        $stokItems = StokBenih::with(['provinsi', 'benih', 'wilayah'])
            ->whereIn('wilayah_id', $activeWilayahIds)
            ->latest()
            ->get();

        // 3. Bungkus ke LengthAwarePaginator agar fungsi links() di view berjalan normal sesuai jumlah halaman kabupaten
        $stokList = new LengthAwarePaginator(
            $stokItems,
            $paginatedWilayahIds->total(),
            $paginatedWilayahIds->perPage(),
            $paginatedWilayahIds->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stokQuery = StokBenih::with(['provinsi', 'benih', 'wilayah']);
        if ($user->hasRole('petugas_provinsi')) {
            $stokQuery->where('provinsi_id', $user->provinsi_id);
        }
        $stokOptions = $stokQuery->get();

        $provinsis = Provinsi::all();

        $wilayahQueryList = Wilayah::query();
        if ($user->hasRole('petugas_provinsi')) {
            $wilayahQueryList->where('provinsi_id', $user->provinsi_id);
        }
        $wilayahs = $wilayahQueryList->orderBy('nama_kabupaten', 'asc')->get();

        $benihQuery = Benih::query();
        if ($user->hasRole('petugas_provinsi')) {
            $benihQuery->where(function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id)
                  ->orWhereNull('provinsi_id');
            });
        }
        $benihs = $benihQuery->get();

        return view('stok.index', compact('stokList', 'stokOptions', 'provinsis', 'wilayahs', 'benihs'));
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
            'provinsi_id'            => 'required|exists:provinsis,id',
            'wilayah_id'             => 'nullable|exists:kabupatens,id',
            'benih_id'               => 'required|exists:benihs,id',
            'kelas_benih'            => 'required|in:FS,SS',
            'jumlah_stok_kg'         => 'required|numeric|min:0',
            'target_ton'             => 'nullable|numeric|min:0',
            'proses_sertifikasi_ton' => 'nullable|numeric|min:0',
            'prosesing_ton'          => 'nullable|numeric|min:0',
        ]);

        if ($user->hasRole('petugas_provinsi')) {
            $validated['provinsi_id'] = $user->provinsi_id;
        }

        $validated['kelas_benih']            = strtoupper($validated['kelas_benih']);
        $validated['target_ton']             = $validated['target_ton'] ?? $stok->target_ton;
        $validated['proses_sertifikasi_ton'] = $validated['proses_sertifikasi_ton'] ?? 0;
        $validated['prosesing_ton']          = $validated['prosesing_ton'] ?? 0;

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

    // TRANSAKSI STOK MASUK (MENAMBAH STOK TERSEDIA, SERTIFIKASI, DAN PROSESING)
    public function storeMasuk(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'wilayah_id'             => 'required|exists:kabupatens,id',
            'benih_id'               => 'required|exists:benihs,id',
            'kelas_benih'            => 'required|in:FS,SS',
            'sumber_tujuan'          => 'required|string|max:150',
            'jumlah_kg'              => 'nullable|numeric|min:0',
            'proses_sertifikasi_ton' => 'nullable|numeric|min:0',
            'prosesing_ton'          => 'nullable|numeric|min:0',
            'tanggal_transaksi'      => 'required|date',
            'keterangan'             => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $wilayah    = Wilayah::findOrFail($validated['wilayah_id']);
            $provinsiId = $user->hasRole('petugas_provinsi') ? $user->provinsi_id : $wilayah->provinsi_id;
            $kelas      = strtoupper($validated['kelas_benih']);

            $volTersedia    = (float) ($validated['jumlah_kg'] ?? 0);
            $volSertifikasi = (float) ($validated['proses_sertifikasi_ton'] ?? 0);
            $volProsesing   = (float) ($validated['prosesing_ton'] ?? 0);

            // Validasi minimal ada satu nilai volume yang diisi > 0
            if (($volTersedia + $volSertifikasi + $volProsesing) <= 0) {
                return back()->withInput()->with('error', 'Minimal salah satu kolom volume (Tersedia, Proses Sertifikasi, atau Prosesing) harus diisi lebih dari 0.');
            }

            $stok = StokBenih::firstOrCreate(
                [
                    'provinsi_id' => $provinsiId,
                    'wilayah_id'  => $validated['wilayah_id'],
                    'benih_id'    => $validated['benih_id'],
                    'kelas_benih' => $kelas,
                ],
                [
                    'jumlah_stok_kg'         => 0.00,
                    'target_ton'             => 0.00,
                    'proses_sertifikasi_ton' => 0.00,
                    'prosesing_ton'          => 0.00,
                ]
            );

            $stokSebelum    = (float) $stok->jumlah_stok_kg;
            $stokSesudah    = $stokSebelum + $volTersedia;
            
            // Total volume yang masuk ditambahkan ke target
            $totalInputMasuk = $volTersedia + $volSertifikasi + $volProsesing;
            $targetBaru      = (float) $stok->target_ton + $totalInputMasuk;

            $stok->update([
                'jumlah_stok_kg'         => $stokSesudah,
                'target_ton'             => $targetBaru,
                'proses_sertifikasi_ton' => (float) $stok->proses_sertifikasi_ton + $volSertifikasi,
                'prosesing_ton'          => (float) $stok->prosesing_ton + $volProsesing,
            ]);

            // Catat transaksi mutasi jika ada stok tersedia yang masuk
            if ($volTersedia > 0) {
                TransaksiStokBenih::create([
                    'stok_benih_id'     => $stok->id,
                    'pengujian_mutu_id' => null,
                    'user_id'           => $user->id,
                    'jenis_transaksi'   => 'masuk',
                    'sumber_tujuan'     => $validated['sumber_tujuan'],
                    'jumlah_kg'         => $volTersedia,
                    'stok_sebelum'      => $stokSebelum,
                    'stok_sesudah'      => $stokSesudah,
                    'tanggal_transaksi' => $validated['tanggal_transaksi'],
                    'keterangan'        => $validated['keterangan'] ?? 'Input stok masuk',
                ]);
            }

            return redirect()->route('stok.index')->with('success', 'Data stok benih kelas ' . $kelas . ' berhasil disimpan!');
        });
    }

    // TRANSAKSI STOK KELUAR / DISTRIBUSI
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

            $stokSebelum = (float) $stok->jumlah_stok_kg;
            $stokSesudah = $stokSebelum - (float) $validated['jumlah_kg'];

            $stok->update([
                'jumlah_stok_kg' => $stokSesudah,
            ]);

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

            return redirect()->route('stok.index')->with('success', 'Distribusi keluar berhasil diproses!');
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
            $query->whereDate('tanggal_transaksi', Carbon::parse($tanggal)->toDateString());
        } else {
            $query->whereBetween('tanggal_transaksi', [
                Carbon::now()->startOfWeek()->toDateString(),
                Carbon::now()->endOfWeek()->toDateString(),
            ]);
        }

        $mutasis = $query->orderBy('tanggal_transaksi', 'desc')
            ->latest()
            ->paginate(5)
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
                $stok->target_ton = max(0, (float) $stok->target_ton + $selisih);
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
                    $stok->target_ton = max(0, (float) $stok->target_ton - (float) $transaksi->jumlah_kg);
                    $stok->save();
                } elseif ($transaksi->jenis_transaksi === 'keluar') {
                    $stok->increment('jumlah_stok_kg', $transaksi->jumlah_kg);
                }
            }

            $transaksi->delete();

            return redirect()->route('stok.riwayat')->with('success', 'Riwayat transaksi mutasi berhasil dihapus!');
        });
    }
}