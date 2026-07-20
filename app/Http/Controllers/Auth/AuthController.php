<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $remember = $request->boolean('remember');

        // Coba sebagai Admin dulu
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            Auth::guard('guru')->logout(); // pastikan tidak ada sesi ganda
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Kalau bukan admin, coba sebagai Guru
        if (Auth::guard('guru')->attempt($credentials, $remember)) {
            Auth::guard('admin')->logout();
            $request->session()->regenerate();
            return redirect()->intended(route('guru.dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('guru')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}