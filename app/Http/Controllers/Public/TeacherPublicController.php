<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Teacher;

class TeacherPublicController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('classes')->orderBy('name')->get();
        return view('public.teachers.index', compact('teachers'));
    }
}