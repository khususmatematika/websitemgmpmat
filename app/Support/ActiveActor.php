<?php
namespace App\Support;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class ActiveActor
{
    /**
     * Return: ['type' => 'student'|'teacher', 'id' => int, 'name' => string, 'identifier' => string] atau null kalau belum login sama sekali.
     */
    public static function current(): ?array
    {
        if (Auth::guard('guru')->check()) {
            $teacher = Auth::guard('guru')->user();
            return [
                'type' => 'teacher',
                'id' => $teacher->id,
                'name' => $teacher->name,
                'identifier' => 'teacher:' . $teacher->id,
            ];
        }

        if (session()->has('student_portal_id')) {
            $student = Student::find(session('student_portal_id'));
            if ($student) {
                return [
                    'type' => 'student',
                    'id' => $student->id,
                    'name' => $student->name,
                    'identifier' => 'student:' . $student->id,
                ];
            }
        }

        return null;
    }

    public static function isLoggedIn(): bool
    {
        return self::current() !== null;
    }
}