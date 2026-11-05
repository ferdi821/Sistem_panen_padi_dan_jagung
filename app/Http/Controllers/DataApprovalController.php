<?php

namespace App\Http\Controllers;

use App\Models\DataApproval;
use Illuminate\Http\Request;

class DataApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tetap gunakan paginate(10) dan relasi lengkap seperti aslimu
        if ($user->hasRole('admin_pusat')) {
            $approvals = DataApproval::with(['user', 'approver'])->latest()->paginate(10);
        } else {
            // Petugas Provinsi hanya melihat data pengajuannya sendiri
            $approvals = DataApproval::with(['user', 'approver'])->where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('approval.index', compact('approvals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_transaksi' => 'required|string',
            'target_id' => 'required|integer',
            'tipe_pengajuan' => 'required|string',
            'alasan' => 'required|string',
        ]);

        DataApproval::create([
            'user_id' => auth()->id(),
            'jenis_transaksi' => $request->jenis_transaksi,
            'target_id' => $request->target_id,
            'tipe_pengajuan' => $request->tipe_pengajuan,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Permohonan koreksi berhasil dikirim ke Admin Pusat.');
    }

    public function approve($id)
    {
        $approval = DataApproval::findOrFail($id);
        $approval->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan koreksi berhasil disetujui.');
    }

    public function reject($id)
    {
        $approval = DataApproval::findOrFail($id);
        $approval->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan koreksi telah ditolak.');
    }
}