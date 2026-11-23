<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->status !== 'approved') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda belum disetujui atau sedang dinonaktifkan.',
                ])->onlyInput('email');
            }
            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user->role);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectBasedOnRole($role)
    {
        if ($role === 'pedagang') {
            return redirect()->route('pedagang.dashboard');
        } elseif ($role === 'mahasiswa') {
            return redirect()->route('mahasiswa.dashboard');
        } elseif ($role === 'wali') {
            return redirect()->route('wali.dashboard');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect('/');
    }
}
