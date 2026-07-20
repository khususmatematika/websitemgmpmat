<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTeacherController extends Controller
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
        $teachers = Teacher::orderBy('name')->paginate(10);
        return view('admin.guru.index', ['teachers' => $teachers] + $this->nav());
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.guru.create', ['classes' => $classes] + $this->nav());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nip' => 'nullable|string|unique:teachers,nip',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|min:8',
            'whatsapp_number' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Teacher::create($data);

        return redirect()->route('admin.guru.index')->with('status', 'Guru berhasil ditambahkan.');
    }

    public function edit(Teacher $guru)
{
    return view('admin.guru.edit', ['teacher' => $guru] + $this->nav());
}

public function update(Request $request, Teacher $guru)
{
    $data = $request->validate([
        'nip' => 'nullable|string|unique:teachers,nip,' . $guru->id,
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:teachers,email,' . $guru->id,
        'whatsapp_number' => 'nullable|string|max:20',
        'title' => 'nullable|string|max:100',
        'description' => 'nullable|string',
        'password' => 'nullable|min:8',
    ]);

    $oldValue = $guru->only(['name', 'email', 'whatsapp_number', 'title']);

    $updateData = collect($data)->except('password')->toArray();
    if (!empty($data['password'])) {
        $updateData['password'] = $data['password'];
    }

    $guru->update($updateData);

    \App\Models\ActivityLog::create([
        'actor_type' => 'admin',
        'actor_id' => \Illuminate\Support\Facades\Auth::guard('admin')->id(),
        'action' => 'override_teacher_data',
        'old_value' => json_encode($oldValue),
        'new_value' => json_encode($guru->fresh()->only(['name', 'email', 'whatsapp_number', 'title'])),
    ]);

    return redirect()->route('admin.guru.index')->with('status', 'Data guru berhasil diperbarui.');
}

    public function destroy(Teacher $guru)
    {
        $guru->delete();
        return back()->with('status', 'Guru berhasil dihapus.');
    }

    public function resetPassword(Request $request, Teacher $guru)
    {
        $request->validate(['password' => 'required|min:8']);
        $guru->update(['password' => $request->password]);
        return back()->with('status', 'Password guru berhasil direset.');
    }
}