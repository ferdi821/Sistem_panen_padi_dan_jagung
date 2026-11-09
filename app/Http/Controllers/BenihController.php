<?php

namespace App\Http\Controllers;

use App\Models\Benih;
use Illuminate\Http\Request;

class BenihController extends Controller
{
    public function index()
    {
        $benihs = Benih::latest()->paginate(5); // Dibatasi 5 baris agar pas seragam
        return view('benih.index', compact('benihs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_benih'   => 'required|string|unique:benihs,kode_benih|max:30',
            'nama_benih'   => 'required|string|max:100',
            'komoditas'    => 'required|in:padi,jagung',
            'varietas'     => 'required|string|max:100',
            'kelas_benih'  => 'required|in:BD,BP,BR',
            'satuan'       => 'required|string|max:20',
            'stok_minimum' => 'required|numeric|min:0',
            'deskripsi'    => 'nullable|string',
        ]);

        Benih::create($validated);

        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil ditambahkan!');
    }

    public function edit(Benih $benih)
    {
        return view('benih.edit', compact('benih'));
    }

    public function update(Request $request, Benih $benih)
    {
        $validated = $request->validate([
            'kode_benih'   => 'required|string|max:30|unique:benihs,kode_benih,' . $benih->id,
            'nama_benih'   => 'required|string|max:100',
            'komoditas'    => 'required|in:padi,jagung',
            'varietas'     => 'required|string|max:100',
            'kelas_benih'  => 'required|in:BD,BP,BR',
            'satuan'       => 'required|string|max:20',
            'stok_minimum' => 'required|numeric|min:0',
            'deskripsi'    => 'nullable|string',
        ]);

        $benih->update($validated);

        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil diperbarui!');
    }

    public function destroy(Benih $benih)
    {
        $benih->delete();
        return redirect()->route('benih.index')->with('success', 'Master Benih berhasil dihapus!');
    }
}