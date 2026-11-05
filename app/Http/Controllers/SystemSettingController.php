<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all();
        if ($settings->isEmpty()) {
            SystemSetting::insert([
                ['key' => 'tahun_anggaran', 'label' => 'Tahun Anggaran Aktif', 'value' => date('Y'), 'type' => 'number'],
                ['key' => 'threshold_stok_kritis', 'label' => 'Ambang Stok Kritis (Ton)', 'value' => '100', 'type' => 'number'],
                ['key' => 'min_kadar_air', 'label' => 'Standar Maksimal Kadar Air (%)', 'value' => '13.0', 'type' => 'text'],
            ]);
            $settings = SystemSetting::all();
        }

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}