<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ], [
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email ini sudah digunakan oleh akun lain.',
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        // Simpan role sebelumnya untuk mencegah hilangnya relasi
        $roles = $user->roles->pluck('name')->toArray();

        $user->update([
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'is_first_login' => false,
        ]);

        // Kembalikan role
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        // Redirect berdasarkan role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang! Data Anda berhasil disimpan.');
        }

        return redirect()->route('pegawai.dashboard')->with('success', 'Selamat datang! Data Anda berhasil disimpan.');
    }
}
