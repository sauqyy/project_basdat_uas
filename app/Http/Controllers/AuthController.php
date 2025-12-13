<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
    
        $login = $request->input('email');
        $password = $request->input('password');
    
        // cek login pakai email, nim, atau nip
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($login) ? 'nim' : 'nip');
    
        if (Auth::attempt([$field => $login, 'password' => $password])) {
            $request->session()->regenerate();
    
            $user = auth()->user();
            
            // Cek apakah user mencoba login dari halaman yang benar
            $isAdminLogin = $request->is('admin/login');
            
            if ($user->isSuperAdmin() || $user->isAdminProdi()) {
                // Admin dan Super Admin hanya bisa login di halaman admin
                if (!$isAdminLogin) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Admin dan Super Admin hanya bisa login di halaman admin.',
                    ]);
                }
                
                if ($user->isSuperAdmin()) {
                    return redirect()->route('super-admin.dashboard');
                } elseif ($user->isAdminProdi()) {
                    return redirect()->route('admin-prodi.dashboard');
                }
            } elseif ($user->isDosen()) {
                // Dosen hanya bisa login di halaman login utama
                if ($isAdminLogin) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Dosen hanya bisa login di halaman login utama.',
                    ]);
                }
                return redirect()->route('dosen.dashboard');
            } else {
                // Jika role tidak valid, logout dan redirect ke login
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses tidak diizinkan. Role tidak valid.',
                ]);
            }
        }
    
        return back()->withErrors([
            'email' => 'Login gagal, periksa kembali data Anda.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
    
}
