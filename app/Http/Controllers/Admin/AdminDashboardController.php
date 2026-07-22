<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;

class AdminDashboardController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\AdminNav::items(),
            'guard' => 'admin',
            'panelTitle' => 'Panel Admin',
        ];
    }

    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_classes' => SchoolClass::count(),
            'total_teachers' => Teacher::count(),
        ];

        return view('admin.dashboard', ['stats' => $stats] + $this->nav());
    }
}