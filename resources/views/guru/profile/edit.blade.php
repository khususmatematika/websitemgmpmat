@extends('layouts.dashboard')
@section('title', 'Profil Saya')

@section('dashboard-content')
<h1 class="font-headline text-2xl font-bold text-navy-deep mb-6">Profil Saya</h1>

@if (session('status'))
    <div class="mb-4 p-3 bg-status-success/10 text-status-success rounded-md text-sm">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 p-3 bg-error-container text-status-error rounded-md text-sm">
        <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('guru.profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-6 max-w-3xl">
    @csrf
    @method('PUT')

    <div class="flex items-center gap-4">
        <img src="{{ $teacher->photo ? asset('storage/'.$teacher->photo) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name) }}"
             class="w-20 h-20 rounded-full object-cover">
        <div>
            <label class="text-sm font-medium text-on-surface-variant block mb-1">Foto Profil</label>
            <input type="file" name="photo" accept="image/*" class="text-sm">
        </div>
    </div>

    <div>
        <label class="text-sm font-medium text-on-surface-variant">Email</label>
        <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium text-on-surface-variant">Nomor WhatsApp</label>
        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $teacher->whatsapp_number) }}" placeholder="628xxxxxxxxxx" class="mt-1 w-full rounded-md border-outline-variant">
    </div>

    <div>
        <label class="text-sm font-medium text-on-surface-variant">Deskripsi Singkat</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-md border-outline-variant">{{ old('description', $teacher->description) }}</textarea>
    </div>

    <div>
        <h3 class="font-headline text-navy-deep mb-3">Kelas & Jadwal Mengajar</h3>
        <div id="schedule-rows" class="space-y-3">
            @php $existing = $teacher->classes; @endphp
            @forelse ($existing as $i => $cls)
            <div class="flex gap-2 items-center schedule-row">
                <select name="schedules[{{ $i }}][class_id]" class="rounded-md border-outline-variant text-sm">
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ $cls->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="schedules[{{ $i }}][day]" class="rounded-md border-outline-variant text-sm">
                    <option value="">Hari</option>
                    @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)
                        <option value="{{ $d }}" {{ $cls->pivot->day == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
                <input type="time" name="schedules[{{ $i }}][start_time]" value="{{ $cls->pivot->start_time ? substr($cls->pivot->start_time, 0, 5) : '' }}" class="rounded-md border-outline-variant text-sm">
                <input type="time" name="schedules[{{ $i }}][end_time]" value="{{ $cls->pivot->end_time ? substr($cls->pivot->end_time, 0, 5) : '' }}" class="rounded-md border-outline-variant text-sm">
                <button type="button" onclick="this.closest('.schedule-row').remove()" class="text-status-error">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </div>
            @empty
            @endforelse
        </div>
        <button type="button" id="add-schedule" class="mt-3 text-sm font-bold text-math-teal">+ Tambah Jadwal</button>
    </div>

    <button type="submit" class="bg-math-teal text-white px-6 py-3 rounded-md font-bold hover:brightness-110">Simpan Perubahan</button>
</form>
@endsection

@push('scripts')
<script>
let idx = {{ $existing->count() ?? 0 }};
document.getElementById('add-schedule').addEventListener('click', () => {
    const classOptions = `@foreach ($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach`;
    const dayOptions = `<option value="">Hari</option>` + `@foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach`;
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center schedule-row';
    row.innerHTML = `
        <select name="schedules[${idx}][class_id]" class="rounded-md border-outline-variant text-sm">${classOptions}</select>
        <select name="schedules[${idx}][day]" class="rounded-md border-outline-variant text-sm">${dayOptions}</select>
        <input type="time" name="schedules[${idx}][start_time]" class="rounded-md border-outline-variant text-sm">
        <input type="time" name="schedules[${idx}][end_time]" class="rounded-md border-outline-variant text-sm">
        <button type="button" onclick="this.closest('.schedule-row').remove()" class="text-status-error">
            <span class="material-symbols-outlined">delete</span>
        </button>`;
    document.getElementById('schedule-rows').appendChild(row);
    idx++;
});
</script>
@endpush