<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBackupController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\AdminNav::items(),
            'guard' => 'admin',
            'panelTitle' => 'Panel Admin',
        ];
    }

    protected function path(string $file): string
    {
        abort_unless(preg_match('/^[A-Za-z0-9_\-]+\.sql$/', $file) === 1, 404);
        $path = DatabaseBackup::dir() . DIRECTORY_SEPARATOR . $file;
        abort_unless(is_file($path), 404);
        return $path;
    }

    public function index()
    {
        $files = collect(glob(DatabaseBackup::dir() . '/*.sql') ?: [])
            ->map(fn($p) => [
                'name' => basename($p),
                'size' => filesize($p),
                'time' => filemtime($p),
            ])
            ->sortByDesc('time')->values();

        return view('admin.backup.index', [
            'files' => $files,
            'dbName' => DB::getDatabaseName(),
        ] + $this->nav());
    }

    public function store()
    {
        try {
            $path = DatabaseBackup::create('manual');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat cadangan: ' . $e->getMessage());
        }

        return back()->with('status', 'Cadangan berhasil dibuat: ' . basename($path) . '. Unduh dan simpan di tempat lain.');
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|max:204800']);

        $file = $request->file('file');
        if (strtolower($file->getClientOriginalExtension()) !== 'sql') {
            return back()->with('error', 'File harus berekstensi .sql.');
        }

        $name = 'backup_' . now()->format('Y-m-d_His') . '_unggahan.sql';
        $file->move(DatabaseBackup::dir(), $name);

        if (!DatabaseBackup::isValid(DatabaseBackup::dir() . DIRECTORY_SEPARATOR . $name)) {
            @unlink(DatabaseBackup::dir() . DIRECTORY_SEPARATOR . $name);
            return back()->with('error', 'File ditolak: bukan cadangan yang dibuat oleh portal ini.');
        }

        return back()->with('status', 'Cadangan berhasil diunggah. Pulihkan dari daftar bila diperlukan.');
    }

    public function download(string $file)
    {
        return response()->download($this->path($file), $file);
    }

    public function restore(Request $request, string $file)
    {
        $request->validate(['confirm' => 'required|in:PULIHKAN'], [
            'confirm.in' => 'Ketik PULIHKAN (huruf besar) untuk konfirmasi.',
        ]);

        $path = $this->path($file);

        try {
            DatabaseBackup::create('sebelumpemulihan'); // pengaman
            $n = DatabaseBackup::restore($path);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan: ' . $e->getMessage() . ' Cadangan pengaman (sebelumpemulihan) tersedia di daftar.');
        }

        return back()->with('status', "Pemulihan selesai ({$n} perintah dijalankan). Cadangan pengaman sebelum pemulihan tetap tersimpan.");
    }

    public function destroy(string $file)
    {
        @unlink($this->path($file));

        return back()->with('status', 'Cadangan dihapus.');
    }
}