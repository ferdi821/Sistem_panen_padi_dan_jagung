<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class PanenController extends Controller
{
    /**
     * Menampilkan daftar catatan panen
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['admin_pusat', 'pimpinan'])) {
            $panenList = CatatanPanen::with(['provinsi', 'user'])->latest()->paginate(10);
        } else {
            $panenList = CatatanPanen::with(['provinsi', 'user'])
                        ->where('provinsi_id', $user->provinsi_id)
                        ->latest()
                        ->paginate(10);
        }

        return view('panen.index', compact('panenList'));
    }

    /**
     * Menampilkan form tambah data
     */
    public function create()
    {
        $provinsis = Provinsi::all();
        return view('panen.create', compact('provinsis'));
    }

    /**
     * Menyimpan data panen ke database
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'komoditas'         => 'required|in:padi,jagung',
            'tanggal_panen'     => 'required|date',
            'luas_lahan_hektar' => 'required|numeric|min:0.01',
            'jumlah_panen_ton'  => 'required|numeric|min:0.01',
            'catatan'           => 'nullable|string',
        ];

        // Admin Pusat wajib memilih provinsi dari dropdown
        if ($user->hasRole('admin_pusat')) {
            $rules['provinsi_id'] = 'required|exists:provinsis,id';
        }

        $validated = $request->validate($rules);

        // Tentukan provinsi_id berdasarkan role
        if ($user->hasRole('admin_pusat')) {
            $validated['provinsi_id'] = $request->provinsi_id;
        } else {
            $validated['provinsi_id'] = $user->provinsi_id;
        }

        // Catat user penginput
        $validated['user_id'] = $user->id;

        // Simpan data
        CatatanPanen::create($validated);

        return redirect()->route('panen.index')->with('success', 'Data panen berhasil disimpan!');
    }

    /**
     * Menghapus data panen dari database
     */
    public function destroy($id)
    {
        $panen = CatatanPanen::findOrFail($id);
        $panen->delete();

        return redirect()->route('panen.index')->with('success', 'Data panen berhasil dihapus!');
    }
}