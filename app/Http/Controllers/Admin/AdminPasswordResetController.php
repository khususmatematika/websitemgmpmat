<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminPasswordResetController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\AdminNav::items(),
            'guard' => 'admin',
            'panelTitle' => 'Panel Admin',
        ];
    }

    public function index(Request $request)
    {
        $type = $request->get('tipe') === 'guru' ? 'guru' : 'siswa';
        $q = trim((string) $request->get('q', ''));

        if ($type === 'guru') {
            $users = Teacher::query()
                ->when($q !== '', fn($x) => $x->where(fn($w) => $w
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%")))
                ->orderBy('name')->paginate(20)->withQueryString();
        } else {
            $users = Student::with('classes')
                ->when($q !== '', fn($x) => $x->where(fn($w) => $w
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")))
                ->orderBy('name')->paginate(20)->withQueryString();
        }

        return view('admin.reset-password.index', [
            'type' => $type,
            'q' => $q,
            'users' => $users,
        ] + $this->nav());
    }

    public function reset(Request $request, string $type, int $id)
    {
        abort_unless(in_array($type, ['guru', 'siswa'], true), 404);

        $data = $request->validate([
            'mode' => 'required|in:random,custom,nis',
            'custom_password' => 'required_if:mode,custom|nullable|string|min:6|max:64',
        ], [
            'custom_password.required_if' => 'Isi sandi baru (minimal 6 karakter).',
            'custom_password.min' => 'Sandi minimal 6 karakter.',
        ]);

        if ($data['mode'] === 'nis' && $type !== 'siswa') {
            return back()->with('error', 'Pilihan "Kembalikan ke NIS" hanya untuk siswa.');
        }

        $user = $type === 'guru' ? Teacher::findOrFail($id) : Student::findOrFail($id);

        $plain = match ($data['mode']) {
            'random' => self::randomPassword(),
            'custom' => $data['custom_password'],
            'nis' => (string) $user->nis,
        };

        $user->password = $plain; // dienkripsi otomatis oleh model
        if ($type === 'guru' && Schema::hasColumn('teachers', 'remember_token')) {
            $user->remember_token = null; // matikan "ingat saya" di perangkat lama
        }
        $user->save();

        try {
            ActivityLog::create([
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('admin')->id(),
                'action' => 'reset_password',
                'old_value' => json_encode(['type' => $type, 'id' => $user->id]),
                'new_value' => json_encode(['mode' => $data['mode']]), // sandi tidak pernah dicatat
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Log reset password gagal: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.reset-password.index', ['tipe' => $type, 'q' => $request->input('q')])
            ->with('reset_info', [
                'name' => $user->name,
                'login' => $type === 'guru' ? $user->email : $user->nis,
                'password' => $plain,
                'is_nis' => $data['mode'] === 'nis',
            ]);
    }

    /** 8 karakter tanpa huruf/angka yang mudah tertukar (0/O, 1/l/I). */
    protected static function randomPassword(int $length = 8): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $out;
    }
}