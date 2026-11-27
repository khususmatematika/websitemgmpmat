<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\Impersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminImpersonateController extends Controller
{
    public function start(Request $request, string $type, int $id)
    {
        abort_unless(in_array($type, ['guru', 'siswa'], true), 404);

        $adminId = Auth::guard('admin')->id();
        $user = $type === 'guru' ? Teacher::findOrFail($id) : Student::findOrFail($id);

        try {
            ActivityLog::create([
                'actor_type' => 'admin',
                'actor_id' => $adminId,
                'action' => 'impersonate_start',
                'old_value' => json_encode(['type' => $type, 'id' => $user->id]),
                'new_value' => json_encode(['name' => $user->name]),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Log peralihan akun gagal: ' . $e->getMessage());
        }

        // Satu peran aktif pada satu waktu
        Auth::guard('guru')->logout();
        session()->forget('student_portal_id');
        Auth::guard('admin')->logout();

        session([
            'impersonator_admin_id' => $adminId,
            'impersonating_type' => $type,
            'impersonating_id' => $user->id,
            'impersonating_name' => $user->name,
        ]);

        if ($type === 'guru') {
            Auth::guard('guru')->login($user);
            $request->session()->regenerate();
            return redirect()->route('guru.dashboard');
        }

        session(['student_portal_id' => $user->id]);
        $request->session()->regenerate();

        return redirect()->route('nilai.show');
    }

    public function stop(Request $request)
    {
        $imp = Impersonation::active();

        if (!$imp) {
            Impersonation::clear();
            abort(403, 'Sesi peralihan akun sudah tidak berlaku.');
        }

        $admin = Admin::find($imp['admin_id']);
        if (!$admin) {
            Impersonation::clear();
            abort(403);
        }

        Auth::guard('guru')->logout();
        session()->forget('student_portal_id');
        Impersonation::clear();

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.reset-password.index');
    }
}