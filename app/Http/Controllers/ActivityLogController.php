<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas (terfilter otomatis berdasarkan role).
     */
    public function index()
    {
        $user = auth()->user();

        // Admin Pusat melihat seluruh log nasional, Petugas hanya melihat log miliknya sendiri
        if ($user->hasRole('admin_pusat')) {
            $logs = ActivityLog::with('user')->latest()->paginate(10);
        } else {
            $logs = ActivityLog::with('user')->where('user_id', $user->id)->latest()->paginate(10);
        }

        return view('logs.index', compact('logs'));
    }

    /**
     * Method helper statis untuk mencatat log aktivitas dari controller lain secara otomatis.
     */
    public static function record($action, $description)
    {
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => strtoupper($action),
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        }
    }
}