<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf kapital, huruf kecil, dan angka.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'is_first_login' => false,
        ]);

        // Redirect berdasarkan role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard')->with('success', 'Password berhasil diperbarui!');
        }

        return redirect()->route('pegawai.dashboard')->with('success', 'Password berhasil diperbarui!');
    }
}
