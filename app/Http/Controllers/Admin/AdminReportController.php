<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StudentWork;
use App\Models\StudentWorkComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReportController extends Controller
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
        $reports = Report::with('reportable')
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->get();

        return view('admin.laporan.index', ['reports' => $reports] + $this->nav());
    }

    public function resolve(Request $request, Report $report)
    {
    $data = $request->validate([
        'action' => 'required|in:hide,dismiss',
        'admin_action' => 'nullable|string|max:255',
    ]);

    if ($data['action'] === 'hide' && $report->reportable) {
        $type = $report->reportable_type;
        $model = $report->reportable;

        if (in_array($type, [\App\Models\StudentWork::class, \App\Models\ForumPost::class])) {
            if (!empty($model->file_path)) {
                \Storage::disk('public')->delete($model->file_path);
            }
            if (!empty($model->image_path)) {
                \Storage::disk('public')->delete($model->image_path);
            }
            $model->delete();
        } elseif (in_array($type, [\App\Models\StudentWorkComment::class, \App\Models\ForumComment::class])) {
            $model->delete();
        }
    }

    $report->update([
        'status' => 'reviewed',
        'admin_action' => $data['admin_action'] ?? ($data['action'] === 'hide' ? 'Konten disembunyikan/dihapus' : 'Ditandai selesai tanpa tindakan'),
    ]);

    return back()->with('status', 'Laporan berhasil ditinjau.');
    }
}