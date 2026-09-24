<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request for Role-Based Access Control.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 1. Validasi Akun Aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan oleh Administrator PT Pinastika Bhakti Semesta.');
        }

        // 2. BOD selalu memiliki hak akses penuh (Full Access Bypass)
        if ($user->role === 'bod') {
            return $next($request);
        }

        // 3. Proteksi Mutasi Data untuk Role Read-Only (Auditor, Komisaris, Viewer)
        // Pengguna dengan role ini hanya diizinkan membaca (GET, HEAD, OPTIONS)
        if ($user->isReadOnly() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses Dibatasi: Role Anda berstatus Read-Only (Hanya Lihat). Anda tidak memiliki hak untuk menambah, mengubah, atau menghapus data sistem.',
                ], 403);
            }

            return back()->with('error', 'Akses Ditolak: Role ' . ($user->roleData->name ?? $user->role) . ' bersifat READ-ONLY (Hanya Pantau). Penambahan, perubahan, atau penghapusan data dibatasi oleh sistem.');
        }

        // 4. Jika tidak ada role spesifik yang diminta, izinkan
        if (empty($roles)) {
            return $next($request);
        }

        // 5. Periksa apakah user memiliki salah satu role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 5. Akses Ditolak
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses modul ini.',
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki wewenang jabatan untuk modul tersebut.');
    }
}
