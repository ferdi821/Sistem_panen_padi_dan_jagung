<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        // Admin Pusat
        $admin = User::create([
            'name' => 'Admin Pusat Kementan',
            'email' => 'admin@pertanian.go.id',
            'password' => Hash::make('admin123'),
            'provinsi_id' => null,
        ]);
        $admin->assignRole('admin_pusat');

        // Pimpinan
        $pimpinan = User::create([
            'name' => 'Pimpinan Pertanian',
            'email' => 'pimpinan@pertanian.go.id',
            'password' => Hash::make('pimpinan123'),
            'provinsi_id' => null,
        ]);
        $pimpinan->assignRole('pimpinan');

        // Petugas Provinsi Jawa Barat (id: 12)
        $petugas = User::create([
            'name' => 'Petugas DTPH Jawa Barat',
            'email' => 'petugas.jabar@pertanian.go.id',
            'password' => Hash::make('petugasjabar123'),
            'provinsi_id' => 12,
        ]);
        $petugas->assignRole('petugas_provinsi');
    }
}
