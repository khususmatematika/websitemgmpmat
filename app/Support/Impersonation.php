<?php
namespace App\Support;

use Illuminate\Support\Facades\Auth;

class Impersonation
{
    public static function active(): ?array
    {
        $adminId = session('impersonator_admin_id');
        $type = session('impersonating_type');
        $uid = session('impersonating_id');

        if (!$adminId || !$type || !$uid) {
            return null;
        }

        $current = $type === 'guru' ? Auth::guard('guru')->id() : session('student_portal_id');

        // Hanya berlaku selama akun yang ditiru memang sedang masuk
        if ((int) $current !== (int) $uid) {
            return null;
        }

        return ['admin_id' => $adminId, 'type' => $type, 'name' => session('impersonating_name')];
    }

    public static function clear(): void
    {
        session()->forget(['impersonator_admin_id', 'impersonating_type', 'impersonating_id', 'impersonating_name']);
    }
}