<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminProdiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        if (!auth()->user()->isAdminProdi()) {
            auth()->logout();
            // Redirect ke halaman login yang sesuai berdasarkan role
            if (auth()->user()->isDosen()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses ditolak. Dosen harus login di halaman login utama.'
                ]);
            } elseif (auth()->user()->isSuperAdmin()) {
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Akses ditolak. Super Admin harus login di halaman admin.'
                ]);
            }
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Akses ditolak. Hanya Admin Prodi yang dapat mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}