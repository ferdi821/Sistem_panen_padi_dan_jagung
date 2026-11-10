<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WilayahController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hasProvinsiIdColumn = Schema::hasColumn('wilayahs', 'provinsi_id');

        // Jika Petugas Provinsi, filter wilayah berdasarkan provinsinya
        if ($user->hasRole('petugas_provinsi')) {
            $query = Wilayah::query();

            if ($hasProvinsiIdColumn) {
                $query->where('provinsi_id', $user->provinsi_id);
            } elseif (isset($user->provinsi->nama_provinsi)) {
                $query->where('nama_provinsi', $user->provinsi->nama_provinsi);
            }

            $wilayahs = $query->latest()->paginate(10);
            $provinsis = Provinsi::where('id', $user->provinsi_id)->get();
        } else {
            // Admin Pusat melihat semua data wilayah
            $wilayahs = $hasProvinsiIdColumn 
                ? Wilayah::with('provinsi')->latest()->paginate(10)
                : Wilayah::latest()->paginate(10);

            $provinsis = Provinsi::all();
        }

        return view('wilayah.index', compact('wilayahs', 'provinsis'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi')) {
            $request->merge(['provinsi_id' => $user->provinsi_id]);
        }

        $validated = $request->validate([
            'kode_wilayah' => 'required|string|max:50|unique:wilayahs,kode_wilayah',
            'provinsi_id'  => 'required|exists:provinsis,id',
            'kabupaten'    => 'required|string|max:100',
            'nama_gudang'  => 'required|string|max:255',
            'kapasitas_ton'=> 'required|numeric|min:0',
            'alamat'       => 'nullable|string',
        ]);

        Wilayah::create($validated);

        return redirect()->route('wilayah.index')->with('success', 'Master Wilayah & Gudang BPSIP berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $wilayah = Wilayah::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi') && isset($wilayah->provinsi_id) && $wilayah->provinsi_id != $user->provinsi_id) {
            abort(403, 'Anda tidak memiliki akses mengedit wilayah di luar provinsi Anda.');
        }

        if ($user->hasRole('petugas_provinsi')) {
            $request->merge(['provinsi_id' => $user->provinsi_id]);
        }

        $validated = $request->validate([
            'kode_wilayah' => 'required|string|max:50|unique:wilayahs,kode_wilayah,' . $id,
            'provinsi_id'  => 'required|exists:provinsis,id',
            'kabupaten'    => 'required|string|max:100',
            'nama_gudang'  => 'required|string|max:255',
            'kapasitas_ton'=> 'required|numeric|min:0',
            'alamat'       => 'nullable|string',
        ]);

        $wilayah->update($validated);

        return redirect()->route('wilayah.index')->with('success', 'Master Wilayah & Gudang BPSIP berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $wilayah = Wilayah::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi') && isset($wilayah->provinsi_id) && $wilayah->provinsi_id != $user->provinsi_id) {
            abort(403, 'Anda tidak memiliki akses menghapus wilayah di luar provinsi Anda.');
        }

        $wilayah->delete();

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil dihapus!');
    }
}