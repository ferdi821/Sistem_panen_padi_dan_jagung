<?php

namespace App\Http\Controllers;

use App\Models\PengujianMutuBenih;
use App\Models\CatatanPanen;
use App\Models\Benih;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class PengujianMutuController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PengujianMutuBenih::with(['catatanPanen.provinsi', 'benih', 'user']);

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $query->whereHas('catatanPanen', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        $pengujianMutus = $query->latest()->get();

        return view('pengujian.index', compact('pengujianMutus'));
    }

    public function create()
    {
        $user = auth()->user();
        $queryPanen = CatatanPanen::query();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $queryPanen->where('provinsi_id', $user->provinsi_id);
        }

        $catatanPanens = $queryPanen->latest()->get();
        $benihs = Benih::all();

        return view('pengujian.create', compact('catatanPanens', 'benihs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catatan_panen_id' => 'required|exists:catatan_panens,id',
            'benih_id' => 'required|exists:benihs,id',
            'kadar_air_persen' => 'required|numeric',
            'daya_tumbuh_persen' => 'required|numeric',
            'jumlah_lolos_kg' => 'required|numeric',
        ]);

        // 1. Ambil ambang batas dari Pengaturan Sistem (SystemSetting)
        $maxKadarAir = (float) (SystemSetting::where('key', 'min_kadar_air')->value('value') ?? 13.0);
        $minDayaTumbuh = (float) (SystemSetting::where('key', 'min_daya_tumbuh')->value('value') ?? 80.0);

        $kadarAir = (float) $request->kadar_air_persen;
        $dayaTumbuh = (float) $request->daya_tumbuh_persen;

        // 2. Evaluasi Logika Kelayakan Berdasarkan Setting
        // Kadar air HARUS <= standar maksimal DAN daya tumbuh HARUS >= standar minimal
        if ($kadarAir <= $maxKadarAir && $dayaTumbuh >= $minDayaTumbuh) {
            $statusVal = 'layak';
        } else {
            $statusVal = 'tidak_layak';
        }

        // Jika user memilih 'proses' secara eksplisit di form, pertahankan 'proses'
        if ($request->has('status_kelayakan') && strtolower(trim($request->status_kelayakan)) === 'proses') {
            $statusVal = 'proses';
        }

        PengujianMutuBenih::create([
            'catatan_panen_id' => $request->catatan_panen_id,
            'benih_id' => $request->benih_id,
            'user_id' => auth()->id(),
            'tanggal_uji' => $request->tanggal_uji ?? now(),
            'volume_sampel_kg' => $request->volume_sampel_kg ?? 0,
            'kadar_air_persen' => $request->kadar_air_persen,
            'daya_tumbuh_persen' => $request->daya_tumbuh_persen,
            'jumlah_lolos_kg' => $request->jumlah_lolos_kg,
            'status_kelayakan' => $statusVal,
            'catatan_pengujian' => $request->catatan_pengujian,
        ]);

        return redirect()->route('pengujian.index')->with('success', 'Data pengujian mutu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pengujianMutu = PengujianMutuBenih::findOrFail($id);
        $user = auth()->user();
        
        $queryPanen = CatatanPanen::query();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $queryPanen->where('provinsi_id', $user->provinsi_id);
        }
        $catatanPanens = $queryPanen->latest()->get();
        $benihs = Benih::all();

        return view('pengujian.edit', compact('pengujianMutu', 'catatanPanens', 'benihs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'benih_id' => 'required|exists:benihs,id',
            'kadar_air_persen' => 'required|numeric',
            'daya_tumbuh_persen' => 'required|numeric',
            'jumlah_lolos_kg' => 'required|numeric',
        ]);

        $pengujianMutu = PengujianMutuBenih::findOrFail($id);

        // 1. Ambil ambang batas dari Pengaturan Sistem (SystemSetting)
        $maxKadarAir = (float) (SystemSetting::where('key', 'min_kadar_air')->value('value') ?? 13.0);
        $minDayaTumbuh = (float) (SystemSetting::where('key', 'min_daya_tumbuh')->value('value') ?? 80.0);

        $kadarAir = (float) $request->kadar_air_persen;
        $dayaTumbuh = (float) $request->daya_tumbuh_persen;

        // 2. Evaluasi Logika Kelayakan Berdasarkan Setting
        if ($kadarAir <= $maxKadarAir && $dayaTumbuh >= $minDayaTumbuh) {
            $statusVal = 'layak';
        } else {
            $statusVal = 'tidak_layak';
        }

        // Jika form mengirim status khusus seperti 'proses'
        if ($request->has('status') && strtolower(trim($request->status)) === 'proses') {
            $statusVal = 'proses';
        }

        $pengujianMutu->update([
            'benih_id' => $request->benih_id,
            'kadar_air_persen' => $request->kadar_air_persen,
            'daya_tumbuh_persen' => $request->daya_tumbuh_persen,
            'jumlah_lolos_kg' => $request->jumlah_lolos_kg,
            'status_kelayakan' => $statusVal,
        ]);

        return redirect()->route('pengujian.index')->with('success', 'Data pengujian mutu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengujianMutu = PengujianMutuBenih::findOrFail($id);
        $pengujianMutu->delete();

        return redirect()->route('pengujian.index')->with('success', 'Data pengujian mutu berhasil dihapus.');
    }
}