@extends('layouts.dashboard')
@section('title', 'Reset Password')

@section('dashboard-content')
<div>
    <h1 class="font-headline text-2xl font-bold text-navy-deep">Reset Password Pengguna</h1>
    <p class="text-on-surface-variant text-sm">Cari pengguna, lalu reset sandinya. Sandi baru hanya tampil sekali setelah reset.</p>
</div>

@if (session('reset_info'))
@php $info = session('reset_info'); @endphp
<div class="bg-status-success/10 border border-status-success/30 rounded-xl p-5">
    <p class="font-bold text-status-success flex items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span> Sandi {{ $info['name'] }} berhasil direset
    </p>
    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div class="bg-white rounded-lg p-3 border border-outline-variant/40">
            <p class="text-[10px] uppercase tracking-wide text-on-surface-variant">{{ $type === 'guru' ? 'Email' : 'NIS' }} (untuk masuk)</p>
            <p class="font-bold text-navy-deep break-all">{{ $info['login'] }}</p>
        </div>
        <div class="bg-white rounded-lg p-3 border border-outline-variant/40 flex items-center justify-between gap-2">
            <div>
                <p class="text-[10px] uppercase tracking-wide text-on-surface-variant">Sandi baru{{ $info['is_nis'] ? ' (sama dengan NIS)' : '' }}</p>
                <p class="font-bold text-navy-deep font-label text-base" id="new-password">{{ $info['password'] }}</p>
            </div>
            <button type="button" onclick="copyPassword()" class="text-math-teal text-xs font-bold whitespace-nowrap" id="copy-btn">Salin</button>
        </div>
    </div>
    <p class="text-xs text-on-surface-variant mt-3">Sampaikan langsung kepada yang bersangkutan dan sarankan menggantinya setelah masuk. Sandi ini tidak bisa ditampilkan lagi setelah halaman ditutup.</p>
</div>
@endif

@if (session('error'))
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ session('error') }}</div>
@endif
@if ($errors->any())
<div class="p-3 bg-error-container text-status-error rounded-md text-sm">{{ $errors->first() }}</div>
@endif

<div class="flex flex-col md:flex-row md:items-center gap-3">
    <div class="flex gap-2">
        <a href="{{ route('admin.reset-password.index', ['tipe' => 'siswa']) }}"
           class="px-4 py-2 rounded-full text-sm font-bold {{ $type === 'siswa' ? 'bg-navy-deep text-white' : 'bg-white border border-outline-variant text-on-surface-variant' }}">Siswa</a>
        <a href="{{ route('admin.reset-password.index', ['tipe' => 'guru']) }}"
           class="px-4 py-2 rounded-full text-sm font-bold {{ $type === 'guru' ? 'bg-navy-deep text-white' : 'bg-white border border-outline-variant text-on-surface-variant' }}">Guru</a>
    </div>
    <form method="GET" class="flex-1 flex gap-2">
        <input type="hidden" name="tipe" value="{{ $type }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $type === 'guru' ? 'Cari nama, email, atau NIP...' : 'Cari nama atau NIS...' }}"
               class="flex-1 rounded-md border-outline-variant text-sm">
        <button class="bg-math-teal text-white px-5 py-2 rounded-md font-bold text-sm">Cari</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead class="bg-surface-container-low text-on-surface-variant">
                <tr>
                    <th class="p-4 text-left">Nama</th>
                    <th class="p-4 text-left">{{ $type === 'guru' ? 'Email / NIP' : 'NIS / Kelas' }}</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr class="border-t border-outline-variant">
                    <td class="p-4 font-medium text-navy-deep">{{ $u->name }}</td>
                    <td class="p-4 text-xs text-on-surface-variant">
                        @if ($type === 'guru')
                            {{ $u->email }}<br>NIP {{ $u->nip ?: '-' }}
                        @else
                            {{ $u->nis }}<br>{{ $u->classes->pluck('name')->implode(', ') ?: 'Belum ada kelas' }}
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-4 whitespace-nowrap">
                            <form method="POST" action="{{ route('admin.impersonate.start', ['type' => $type, 'id' => $u->id]) }}"
                                onsubmit="return confirm(@js('Masuk sebagai ' . $u->name . '? Semua tindakan tercatat atas akun tersebut.'))">
                                @csrf
                                <button class="text-math-teal text-xs font-bold">Masuk sebagai</button>
                            </form>
                            <button type="button"
                                    onclick="openReset('{{ $type }}', {{ $u->id }}, @js($u->name))"
                                    class="text-status-error text-xs font-bold">Reset</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-10 text-center text-on-surface-variant">Tidak ada pengguna ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div>{{ $users->links() }}</div>

<div id="reset-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="font-headline text-lg font-bold text-navy-deep mb-1">Reset Password</h3>
        <p class="text-sm text-on-surface-variant mb-4">Pengguna: <strong id="reset-name"></strong></p>

        <form id="reset-form" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="q" value="{{ $q }}">

            <label class="flex items-start gap-2 border border-outline-variant rounded-md p-3 cursor-pointer has-[:checked]:border-math-teal has-[:checked]:bg-math-teal/5">
                <input type="radio" name="mode" value="random" checked onchange="toggleCustom()" class="mt-1 text-math-teal">
                <span class="text-sm"><strong>Sandi acak</strong><br><span class="text-xs text-on-surface-variant">Sistem membuat 8 karakter, ditampilkan sekali.</span></span>
            </label>

            <label class="flex items-start gap-2 border border-outline-variant rounded-md p-3 cursor-pointer has-[:checked]:border-math-teal has-[:checked]:bg-math-teal/5">
                <input type="radio" name="mode" value="custom" onchange="toggleCustom()" class="mt-1 text-math-teal">
                <span class="text-sm w-full"><strong>Tentukan sendiri</strong>
                    <input type="text" name="custom_password" id="custom-password" minlength="6" maxlength="64" autocomplete="off"
                           placeholder="Minimal 6 karakter" class="hidden mt-2 w-full rounded-md border-outline-variant text-sm">
                </span>
            </label>

            <label id="nis-option" class="hidden items-start gap-2 border border-outline-variant rounded-md p-3 cursor-pointer has-[:checked]:border-math-teal has-[:checked]:bg-math-teal/5">
                <input type="radio" name="mode" value="nis" onchange="toggleCustom()" class="mt-1 text-math-teal">
                <span class="text-sm"><strong>Kembalikan ke NIS</strong><br><span class="text-xs text-on-surface-variant">Sandi sama dengan NIS, seperti akun baru.</span></span>
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('reset-modal').classList.add('hidden')"
                        class="flex-1 py-2 border border-outline-variant rounded-md text-sm font-bold text-on-surface-variant">Batal</button>
                <button type="submit" class="flex-1 py-2 bg-status-error text-white rounded-md text-sm font-bold">Reset Sandi</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReset(type, id, name) {
    document.getElementById('reset-name').textContent = name;
    document.getElementById('reset-form').action = `/admin/reset-password/${type}/${id}`;

    const nis = document.getElementById('nis-option');
    nis.classList.toggle('hidden', type !== 'siswa');
    nis.classList.toggle('flex', type === 'siswa');

    document.querySelector('input[name="mode"][value="random"]').checked = true;
    toggleCustom();
    document.getElementById('reset-modal').classList.remove('hidden');
}

function toggleCustom() {
    const custom = document.querySelector('input[name="mode"]:checked').value === 'custom';
    const field = document.getElementById('custom-password');
    field.classList.toggle('hidden', !custom);
    field.required = custom;
    if (!custom) field.value = '';
}

function copyPassword() {
    const text = document.getElementById('new-password').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        document.getElementById('copy-btn').textContent = 'Tersalin';
    });
}
</script>
@endsection