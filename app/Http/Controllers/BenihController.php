<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use App\Models\StokBenih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BenihController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Benih::query();

        if ($user->hasRole('petugas_provinsi')) {
            $query->where(function ($q) use ($user) {
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

        $request->validate([
            'kode_benih'   => 'required|string|max:50|unique:benihs,kode_benih',
            'nama_benih'   => 'required|string|max:255',
            'komoditas'    => 'required|string',
            'varietas'     => 'required|string|max:255',
            'stok_minimum' => 'required|numeric|min:0',
        ]);

        $provinsiId = $user->hasRole('petugas_provinsi') ? $user->provinsi_id : null;

        Benih::create([
            'kode_benih'   => $request->kode_benih,
            'nama_benih'   => $request->nama_benih,
            'komoditas'    => $request->komoditas,
            'varietas'     => $request->varietas,
            'stok_minimum' => $request->stok_minimum,
            'provinsi_id'  => $provinsiId,
        ]);

        return redirect()->route('benih.index')->with('success', 'Master Data Varietas Benih berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $benih = Benih::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('petugas_provinsi') && $benih->provinsi_id && $benih->provinsi_id != $user->provinsi_id) {
            return back()->with('error', 'Akses ditolak! Anda tidak berhak mengedit benih provinsi lain.');
        }

        $request->validate([
            'kode_benih'   => 'required|string|max:50|unique:benihs,kode_benih,' . $id,
            'nama_benih'   => 'required|string|max:255',
            'komoditas'    => 'required|string',
            'varietas'     => 'required|string|max:255',
            'stok_minimum' => 'required|numeric|min:0',
        ]);

        $benih->update([
            'kode_benih'   => $request->kode_benih,
            'nama_benih'   => $request->nama_benih,
            'komoditas'    => $request->komoditas,
            'varietas'     => $request->varietas,
            'stok_minimum' => $request->stok_minimum,
        ]);

        return redirect()->route('benih.index')->with('success', 'Master Data Varietas Benih berhasil diperbarui!');
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

        return redirect()->route('benih.index')->with('success', 'Master Data Varietas Benih berhasil dihapus!');
    }
}