<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use Illuminate\Http\Request;

class BenihController extends Controller
{
    public function index()
    {
        $benihs = Benih::latest()->paginate(10);
        return view('benih.index', compact('benihs'));
    }

    public function create()
    {
        return view('benih.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_benih'    => 'required|string|max:50|unique:benihs,kode_benih',
            'nama_benih'    => 'required|string|max:255',
            'komoditas'     => 'required|string|in:padi,jagung',
            'varietas'      => 'required|string|max:100',
            'kelas_benih'   => 'required|string|max:50',
            'stok_min_ton'  => 'required|numeric|min:0',
        ]);

        Benih::create($validated);

        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $benih = Benih::findOrFail($id);
        return view('benih.edit', compact('benih'));
    }

    public function update(Request $request, $id)
    {
        $benih = Benih::findOrFail($id);

        $validated = $request->validate([
            'kode_benih'    => 'required|string|max:50|unique:benihs,kode_benih,' . $id,
            'nama_benih'    => 'required|string|max:255',
            'komoditas'     => 'required|string|in:padi,jagung',
            'varietas'      => 'required|string|max:100',
            'kelas_benih'   => 'required|string|max:50',
            'stok_min_ton'  => 'required|numeric|min:0',
        ]);

        $benih->update($validated);

        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $benih = Benih::findOrFail($id);
        
        // Cek jika benih sudah digunakan di stok
        if ($benih->stokBenihs()->exists()) {
            return back()->with('error', 'Gagal menghapus! Benih ini sudah terikat dengan data stok wilayah.');
        }

        $benih->delete();

        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil dihapus!');
    }
}