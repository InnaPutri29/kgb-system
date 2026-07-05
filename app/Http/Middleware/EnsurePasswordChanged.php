<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Paksa pegawai yang baru login pertama kali untuk ganti password.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_first_login) {
            // Jangan redirect kalau sudah di halaman ganti password
            if (!$request->routeIs('password.change') && !$request->routeIs('password.change.update')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Anda harus mengganti password sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
