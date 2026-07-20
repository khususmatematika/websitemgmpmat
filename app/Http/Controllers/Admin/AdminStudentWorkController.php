<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminStudentWorkController extends Controller
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
        $status = $request->get('status', 'pending');

        $works = StudentWork::withCount('likes')
            ->where('status', $status)
            ->latest()
            ->get();

        return view('admin.karya-siswa.index', ['works' => $works, 'status' => $status] + $this->nav());
    }

    public function approve(StudentWork $studentWork)
    {
        $studentWork->update(['status' => 'approved', 'rejection_reason' => null]);
        return back()->with('status', 'Karya berhasil disetujui dan tayang ke publik.');
    }

    public function reject(Request $request, StudentWork $studentWork)
    {
        $data = $request->validate(['rejection_reason' => 'nullable|string|max:255']);
        $studentWork->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'] ?? null]);
        return back()->with('status', 'Karya ditolak.');
    }

    public function destroy(StudentWork $studentWork)
    {
        Storage::disk('public')->delete($studentWork->file_path);
        $studentWork->delete();
        return back()->with('status', 'Karya berhasil dihapus permanen.');
    }
}