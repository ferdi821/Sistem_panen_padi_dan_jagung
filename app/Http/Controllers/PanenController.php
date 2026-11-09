<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class PanenController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = CatatanPanen::with('provinsi');

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        // Batasi maksimal 5 data per halaman (bisa dislide/pindah halaman jika lebih dari 5)
        $panens = $query->latest()->paginate(5);
        
        $provinsis = Provinsi::orderBy('nama_provinsi', 'asc')->get();

        return view('panen.index', compact('panens', 'provinsis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provinsi_id'      => 'required|exists:provinsis,id',
            'komoditas'        => 'required|string|in:padi,jagung',
            'tanggal_panen'    => 'required|date',
            'luas_lahan_hektar'=> 'required|numeric|min:0.01', // Diubah ke _hektar
            'jumlah_panen_ton' => 'required|numeric|min:0.01',
            'catatan'          => 'nullable|string',
        ]);

        CatatanPanen::create([
            'provinsi_id'      => $validated['provinsi_id'],
            'user_id'          => auth()->id(),
            'komoditas'        => $validated['komoditas'],
            'tanggal_panen'    => $validated['tanggal_panen'],
            'luas_lahan_hektar'=> $validated['luas_lahan_hektar'], // Diubah ke _hektar
            'jumlah_panen_ton' => $validated['jumlah_panen_ton'],
            'catatan'          => $validated['catatan'],
        ]);

        return redirect()->route('panen.index')->with('success', 'Catatan hasil panen berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'provinsi_id'      => 'required|exists:provinsis,id',
            'komoditas'        => 'required|string|in:padi,jagung',
            'tanggal_panen'    => 'required|date',
            'luas_lahan_hektar'=> 'required|numeric|min:0.01', // Diubah ke _hektar
            'jumlah_panen_ton' => 'required|numeric|min:0.01',
            'catatan'          => 'nullable|string',
        ]);

        $panen = CatatanPanen::findOrFail($id);
        $panen->update([
            'provinsi_id'      => $validated['provinsi_id'],
            'komoditas'        => $validated['komoditas'],
            'tanggal_panen'    => $validated['tanggal_panen'],
            'luas_lahan_hektar'=> $validated['luas_lahan_hektar'], // Diubah ke _hektar
            'jumlah_panen_ton' => $validated['jumlah_panen_ton'],
            'catatan'          => $validated['catatan'],
        ]);

        return redirect()->route('panen.index')->with('success', 'Catatan hasil panen berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $panen = CatatanPanen::findOrFail($id);
        $panen->delete();

        return redirect()->route('panen.index')->with('success', 'Catatan hasil panen berhasil dihapus!');
    }
}