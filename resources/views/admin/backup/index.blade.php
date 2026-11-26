@extends('layouts.dashboard')
@section('title', 'Backup Data')

@section('dashboard-content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div>
        <h1 class="font-headline text-2xl font-bold text-navy-deep">Backup Data</h1>
        <p class="text-on-surface-variant text-sm">Database: <strong>{{ $dbName }}</strong>. Cadangan berisi seluruh data portal.</p>
    </div>
    <form method="POST" action="{{ route('admin.backup.store') }}">
        @csrf
        <button onclick="this.disabled=true; this.innerText='Membuat cadangan...'; this.form.submit();"
                class="flex items-center gap-2 bg-math-teal text-white px-5 py-2.5 rounded-md font-bold text-sm hover:brightness-110 whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">backup</span>
            Buat Cadangan Sekarang
        </button>
    </form>
</div>

@if (session('status'))
<div class="p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if (session('error'))
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
@endif

<div class="bg-status-warning/10 border border-status-warning/30 rounded-xl p-4 text-xs text-on-surface-variant space-y-1">
    <p class="font-bold text-navy-deep text-sm">Yang perlu diketahui</p>
    <ul class="list-disc list-inside space-y-0.5">
        <li><strong>Unduh</strong> cadangan dan simpan di komputer atau Google Drive. Cadangan yang hanya ada di server ikut hilang bila server bermasalah.</li>
        <li>Buat cadangan <strong>sebelum</strong> ganti semester, naik kelas, impor siswa, dan pembaruan portal.</li>
        <li>Cadangan ini mencakup database saja. File unggahan (PDF materi, foto karya, sampul) ada di folder <code>storage/app/public</code> dan perlu disalin terpisah.</li>
    </ul>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[620px]">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-4 text-left">File Cadangan</th>
                    <th class="p-4 text-left">Dibuat</th>
                    <th class="p-4 text-right">Ukuran</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($files as $f)
                <tr class="border-t border-outline-variant">
                    <td class="p-4">
                        <span class="font-medium text-navy-deep break-all">{{ $f['name'] }}</span>
                        @if (str_contains($f['name'], '_otomatis'))
                            <span class="ml-1 text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full">otomatis</span>
                        @elseif (str_contains($f['name'], '_sebelumpemulihan'))
                            <span class="ml-1 text-[10px] bg-status-warning/20 text-on-surface px-2 py-0.5 rounded-full">pengaman</span>
                        @endif
                    </td>
                    <td class="p-4 text-on-surface-variant text-xs whitespace-nowrap">{{ \Carbon\Carbon::createFromTimestamp($f['time'])->translatedFormat('d M Y H:i') }}</td>
                    <td class="p-4 text-right text-on-surface-variant text-xs whitespace-nowrap">
                        {{ $f['size'] > 1048576 ? number_format($f['size'] / 1048576, 1) . ' MB' : number_format($f['size'] / 1024, 0) . ' KB' }}
                    </td>
                    <td class="p-4">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            <a href="{{ route('admin.backup.download', $f['name']) }}" class="text-math-teal text-xs font-bold">Unduh</a>
                            <button type="button" onclick="openRestore('{{ $f['name'] }}')" class="text-navy-deep text-xs font-bold">Pulihkan</button>
                            <form method="POST" action="{{ route('admin.backup.destroy', $f['name']) }}" onsubmit="return confirm('Hapus cadangan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-status-error text-xs font-bold">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-10 text-center text-on-surface-variant">Belum ada cadangan. Klik "Buat Cadangan Sekarang".</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6">
    <h2 class="font-headline text-lg font-bold text-navy-deep mb-1">Unggah Cadangan</h2>
    <p class="text-xs text-on-surface-variant mb-3">Untuk memindahkan data dari komputer lain atau memulihkan dari file yang pernah diunduh. Hanya file cadangan buatan portal ini yang diterima.</p>
    <form method="POST" action="{{ route('admin.backup.upload') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        @csrf
        <input type="file" name="file" accept=".sql" required class="text-sm">
        <button class="bg-navy-deep text-white px-5 py-2 rounded-md font-bold text-sm hover:bg-math-teal transition-colors">Unggah</button>
    </form>
</div>

<div id="restore-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="font-headline text-lg font-bold text-status-error mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined">warning</span> Pulihkan Data
        </h3>
        <p class="text-sm text-on-surface mb-1">File: <strong id="restore-name" class="break-all"></strong></p>
        <p class="text-xs text-on-surface-variant mb-4">
            Seluruh data saat ini akan <strong>diganti</strong> dengan isi cadangan ini. Sistem membuat cadangan pengaman otomatis sebelum memulihkan.
            Proses bisa memakan waktu beberapa menit, jangan tutup halaman.
        </p>
        <form id="restore-form" method="POST">
            @csrf
            <label class="text-sm font-medium">Ketik <strong>PULIHKAN</strong> untuk konfirmasi</label>
            <input type="text" name="confirm" required autocomplete="off" class="mt-1 w-full rounded-md border-outline-variant text-sm">
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('restore-modal').classList.add('hidden')"
                        class="flex-1 py-2 border border-outline-variant rounded-md text-sm font-bold text-on-surface-variant">Batal</button>
                <button type="submit" onclick="this.innerText='Memulihkan...'"
                        class="flex-1 py-2 bg-status-error text-white rounded-md text-sm font-bold">Pulihkan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestore(name) {
    document.getElementById('restore-name').textContent = name;
    document.getElementById('restore-form').action = `/admin/backup/${name}/pulihkan`;
    document.getElementById('restore-modal').classList.remove('hidden');
}
</script>
@endsection