<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder {
    public function run(): void {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Permission
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'view panen']);
        Permission::create(['name' => 'create panen']);
        Permission::create(['name' => 'delete panen']);
        Permission::create(['name' => 'view rekap nasional']);

        // 1. Role Admin Pusat (Akses Penuh)
        $roleAdmin = Role::create(['name' => 'admin_pusat']);
        $roleAdmin->givePermissionTo(Permission::all());

        // 2. Role Petugas Provinsi (Hanya CRUD data di wilayahnya)
        $rolePetugas = Role::create(['name' => 'petugas_provinsi']);
        $rolePetugas->givePermissionTo([
            'view dashboard',
            'view panen',
            'create panen',
            'delete panen'
        ]);

        // 3. Role Pimpinan (Hanya Lihat Laporan & Rekap)
        $rolePimpinan = Role::create(['name' => 'pimpinan']);
        $rolePimpinan->givePermissionTo([
            'view dashboard',
            'view panen',
            'view rekap nasional'
        ]);
    }
}
