<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            // Redirect ke halaman login yang sesuai berdasarkan role
            if (Auth::user()->isDosen()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses ditolak. Dosen harus login di halaman login utama.'
                ]);
            }
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}
