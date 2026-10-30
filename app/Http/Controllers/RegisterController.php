<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Mengambil daftar provinsi untuk pilihan petugas daerah
        $provinsis = Provinsi::all();
        return view('auth.register', compact('provinsis'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin_pusat,petugas_provinsi,pimpinan'],
            'provinsi_id' => ['required_if:role,petugas_provinsi', 'nullable', 'exists:provinsis,id'],
        ], [
            'provinsi_id.required_if' => 'Provinsi wajib dipilih untuk akun Petugas Provinsi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // 1. Buat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provinsi_id' => $request->role === 'petugas_provinsi' ?$request->provinsi_id : null,
        ]);

        // 2. Assign Role Spatie
        $user->assignRole($request->role);

        // 3. Otomatis Login setelah Registrasi
        Auth::login($user);

        // 4. Redirect ke Dashboard
        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di Sistem Panen Nasional.');
    }
}
