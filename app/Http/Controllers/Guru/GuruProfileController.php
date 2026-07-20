<?php
namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruProfileController extends Controller
{
    protected function nav(): array
    {
        return [
            'navItems' => \App\Support\GuruNav::items(),
            'guard' => 'guru',
            'panelTitle' => 'Panel Guru',
        ];
    }

    public function edit()
    {
        $teacher = Auth::guard('guru')->user();
        $classes = SchoolClass::orderBy('name')->get();

        return view('guru.profile.edit', [
            'teacher' => $teacher->load('classes'),
            'classes' => $classes,
        ] + $this->nav());
    }

    public function update(Request $request)
    {
        $teacher = Auth::guard('guru')->user();

        // Sanitasi jadwal sebelum divalidasi:
        // - Potong ke format HH:MM saja (kalau ada detik "HH:MM:SS")
        // - Kosongkan jadi null kalau memang tidak diisi
        $schedules = $request->input('schedules', []);
        foreach ($schedules as $i => $sch) {
            foreach (['start_time', 'end_time'] as $field) {
                $value = trim((string) ($sch[$field] ?? ''));
                $schedules[$i][$field] = $value !== '' ? substr($value, 0, 5) : null;
            }
        }
        $request->merge(['schedules' => $schedules]);

        $data = $request->validate([
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'whatsapp_number' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:2048',
            'schedules' => 'nullable|array',
            'schedules.*.class_id' => 'required_with:schedules|exists:classes,id',
            'schedules.*.day' => 'nullable|string',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i',
        ]);

        $oldValue = $teacher->only(['email', 'whatsapp_number', 'description']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update(collect($data)->except(['schedules', 'photo'])->toArray() + ($request->hasFile('photo') ? ['photo' => $data['photo']] : []));

        if ($request->has('schedules')) {
            $sync = [];
            foreach ($request->schedules as $s) {
                if (empty($s['class_id'])) continue;
                $sync[$s['class_id']] = [
                    'day' => $s['day'] ?? null,
                    'start_time' => $s['start_time'] ?? null,
                    'end_time' => $s['end_time'] ?? null,
                ];
            }
            $teacher->classes()->sync($sync);
        }

        ActivityLog::create([
            'actor_type' => 'teacher',
            'actor_id' => $teacher->id,
            'action' => 'update_profile_schedule',
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($teacher->fresh()->only(['email', 'whatsapp_number', 'description'])),
        ]);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}