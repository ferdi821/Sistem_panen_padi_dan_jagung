<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\Provinsi;
use App\Exports\PanenExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PanenController extends Controller {
    public function index() {
        $user = auth()->user();
        $query = CatatanPanen::with(['provinsi', 'user']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        $panenList = $query->latest()->paginate(15);
        return view('panen.index', compact('panenList'));
    }

    public function create() {
        $provinsis = Provinsi::all();
        return view('panen.create', compact('provinsis'));
    }

    public function store(Request $request) {
        $user = auth()->user();

        $rules = [
            'komoditas' => 'required|in:padi,jagung',
            'tanggal_panen' => 'required|date',
            'luas_lahan_hektar' => 'required|numeric|min:0.01',
            'jumlah_panen_ton' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string'
        ];

        if ($user->hasRole('admin_pusat')) {
            $rules['provinsi_id'] = 'required|exists:provinsis,id';
        }

        $validated = $request->validate($rules);

        CatatanPanen::create([
            'provinsi_id' => $user->hasRole('petugas_provinsi') ? $user->provinsi_id : $validated['provinsi_id'],
            'user_id' => $user->id,
            'komoditas' => $validated['komoditas'],
            'tanggal_panen' => $validated['tanggal_panen'],
            'luas_lahan_hektar' => $validated['luas_lahan_hektar'],
            'jumlah_panen_ton' => $validated['jumlah_panen_ton'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('panen.index')->with('success', 'Data panen berhasil disimpan!');
    }

    public function destroy(CatatanPanen $panen) {
        $user = auth()->user();

        if ($user->hasRole('petugas_provinsi') && $panen->provinsi_id !== $user->provinsi_id) {
            abort(403, 'Anda tidak berhak menghapus data dari provinsi lain.');
        }

        $panen->delete();
        return redirect()->route('panen.index')->with('success', 'Data panen berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        // Batasi wilayah jika pengguna bertindak sebagai petugas_provinsi
        $provinsiId = $user->hasRole('petugas_provinsi') ? $user->provinsi_id : $request->query('provinsi_id');
        $komoditas = $request->query('komoditas');

        $fileName = 'Laporan_Panen_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PanenExport($provinsiId, $komoditas), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $query = CatatanPanen::with(['provinsi', 'user']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        } elseif ($request->has('provinsi_id') && $request->provinsi_id != '') {
            $query->where('provinsi_id', $request->provinsi_id);
        }

        if ($request->has('komoditas') && $request->komoditas != '') {
            $query->where('komoditas', $request->komoditas);
        }

        $panenList = $query->latest('tanggal_panen')->get();

        $pdf = Pdf::loadView('reports.panen_pdf', compact('panenList'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Panen_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
