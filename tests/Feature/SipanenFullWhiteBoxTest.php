<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Provinsi;
use App\Models\StokBenih;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SipanenFullWhiteBoxTest extends TestCase
{
    // Otomatis membatalkan perubahan database setelah test selesai (data asli tetap aman)
    use DatabaseTransactions;

    /**
     * 1. Uji Percabangan Dashboard Berdasarkan 3 Role Utama
     */
    public function test_percabangan_view_dashboard_semua_role(): void
    {
        $roleViews = [
            'admin_pusat' => 'dashboard.index',
            'petugas_provinsi' => 'dashboard.petugas',
            'pimpinan' => 'dashboard.pimpinan',
        ];

        foreach ($roleViews as $roleName => $expectedView) {
            $user = User::whereHas('roles', fn($q) => $q->where('name', $roleName))->first();
            if (!$user) {
                continue;
            }

            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertViewIs($expectedView);
        }
    }

    /**
     * 2. Uji Matriks Proteksi Middleware Antar-Role (RBAC)
     */
    public function test_matriks_proteksi_middleware_lintas_role(): void
    {
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'admin_pusat'))->first();
        $petugas = User::whereHas('roles', fn($q) => $q->where('name', 'petugas_provinsi'))->first();
        $pimpinan = User::whereHas('roles', fn($q) => $q->where('name', 'pimpinan'))->first();

        // Petugas dilarang mengakses manajemen user & pengaturan sistem milik Admin Pusat
        if ($petugas) {
            $this->actingAs($petugas)->get('/users')->assertStatus(403);
            $this->actingAs($petugas)->get('/settings')->assertStatus(403);
        }

        // Pimpinan dilarang melakukan input operasional panen & manajemen user
        if ($pimpinan) {
            $this->actingAs($pimpinan)->post('/panen', [])->assertStatus(403);
            $this->actingAs($pimpinan)->get('/users')->assertStatus(403);
        }

        // Admin Pusat dilarang melakukan input transaksi panen milik Petugas Provinsi
        if ($admin) {
            $this->actingAs($admin)->post('/panen', [])->assertStatus(403);
        }
    }

    /**
     * 3. Uji Validasi Input Kosong pada Modul Operasional Panen (Petugas Provinsi)
     */
    public function test_validasi_input_kosong_data_panen(): void
    {
        $petugas = User::whereHas('roles', fn($q) => $q->where('name', 'petugas_provinsi'))->first();
        if (!$petugas) {
            $this->markTestSkipped('Akun petugas_provinsi belum tersedia.');
        }

        $response = $this->actingAs($petugas)->post('/panen', []);
        $response->assertSessionHasErrors();
    }

    /**
     * 4. Uji Logika Pencegahan Stok Minus saat Transaksi Stok Keluar
     */
    public function test_logika_pencegahan_stok_keluar_melebihi_saldo(): void
    {
        $petugas = User::whereHas('roles', fn($q) => $q->where('name', 'petugas_provinsi'))->first();
        $stok = StokBenih::when($petugas?->provinsi_id, fn($q) => $q->where('provinsi_id', $petugas->provinsi_id))->first();

        if (!$petugas || !$stok) {
            $this->markTestSkipped('Data petugas atau stok benih belum tersedia.');
        }

        $jumlahBerlebih = ($stok->jumlah_stok_kg ?? 0) + 999999;

        $response = $this->actingAs($petugas)->post('/stok-benih/keluar', [
            'stok_benih_id' => $stok->id,
            'jumlah_kg' => $jumlahBerlebih,
            'keterangan' => 'Uji batas stok keluar berlebih',
        ]);

        // Sistem harus menolak (redirect back dengan error atau session error) dan saldo tidak berubah
        $response->assertStatus(302);
        $this->assertEquals($stok->jumlah_stok_kg, $stok->fresh()->jumlah_stok_kg);
    }

    /**
     * 5. Uji Jalur Halaman Laporan Rekap Nasional & Audit Log untuk Pimpinan
     */
    public function test_jalur_ekspor_laporan_rekap_nasional(): void
    {
        $pimpinan = User::whereHas('roles', fn($q) => $q->where('name', 'pimpinan'))->first();
        if (!$pimpinan) {
            $this->markTestSkipped('Akun pimpinan belum tersedia.');
        }

        $this->actingAs($pimpinan)->get('/admin/rekap-bbpsip')->assertStatus(200);
        $this->actingAs($pimpinan)->get('/logs')->assertStatus(200);
        $this->actingAs($pimpinan)->get('/wilayah')->assertStatus(200);
        $this->actingAs($pimpinan)->get('/benih')->assertStatus(200);
    }
}