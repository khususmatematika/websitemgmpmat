<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
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
            'identifier' => ['required', 'string'], // email (guru/admin) atau NIS (siswa)
            'password' => ['required'],
        ]);

        $identifier = trim($credentials['identifier']);
        $remember = $request->boolean('remember');

        // 1. Coba sebagai Admin (pakai email)
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            if (Auth::guard('admin')->attempt(['email' => $identifier, 'password' => $credentials['password']], $remember)) {
                Auth::guard('guru')->logout();
                session()->forget('student_portal_id');
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // 2. Coba sebagai Guru (pakai email)
            if (Auth::guard('guru')->attempt(['email' => $identifier, 'password' => $credentials['password']], $remember)) {
                Auth::guard('admin')->logout();
                session()->forget('student_portal_id');
                $request->session()->regenerate();
                return redirect()->intended(route('guru.dashboard'));
            }
        } else {
            // 3. Coba sebagai Siswa (pakai NIS)
            $student = Student::where('nis', $identifier)->first();
            if ($student && $student->checkPassword($credentials['password'])) {
                Auth::guard('admin')->logout();
                Auth::guard('guru')->logout();
                $request->session()->regenerate();
                session(['student_portal_id' => $student->id]);
                return redirect()->intended(route('nilai.show'));
            }
        }

        return back()->withErrors(['identifier' => 'Email/NIS atau password salah.'])->onlyInput('identifier');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('guru')->logout();
        session()->forget('student_portal_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}