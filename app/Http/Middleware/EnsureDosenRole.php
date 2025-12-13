<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDosenRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        if (Auth::user()->role !== 'dosen') {
            Auth::logout();
            // Redirect ke halaman login yang sesuai berdasarkan role
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Akses ditolak. Admin harus login di halaman admin.'
                ]);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Akses ditolak. Hanya dosen yang dapat mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}
