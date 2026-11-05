<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $wilayahs = Wilayah::latest()->paginate(10);
        return view('wilayah.index', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_wilayah' => 'required|unique:wilayahs,kode_wilayah',
            'nama_provinsi' => 'required|string',
            'nama_kabupaten' => 'required|string',
            'nama_gudang' => 'required|string',
            'kapasitas_ton' => 'required|numeric|min:0',
        ]);

        Wilayah::create($request->all());

        return redirect()->route('wilayah.index')->with('success', 'Master Wilayah & Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'kode_wilayah' => 'required|unique:wilayahs,kode_wilayah,' . $wilayah->id,
            'nama_provinsi' => 'required|string',
            'nama_kabupaten' => 'required|string',
            'nama_gudang' => 'required|string',
            'kapasitas_ton' => 'required|numeric|min:0',
        ]);

        $wilayah->update($request->all());

        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        $wilayah->delete();
        return redirect()->route('wilayah.index')->with('success', 'Data Wilayah berhasil dihapus.');
    }
}