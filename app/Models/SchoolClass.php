<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'jenjang', 'fase', 'class_type'];

    /** Guru pengampu kelas ini pada periode AKTIF. */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class', 'class_id', 'teacher_id')
            ->withPivot('day', 'start_time', 'end_time', 'academic_year_id')
            ->wherePivot('academic_year_id', \App\Models\AcademicYear::activeId());
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')->withTimestamps();
    }

    /**
     * Kelas yang diajar seorang guru.
     * $allPeriods = true: kelas dari periode manapun (untuk melihat arsip dan awal semester sebelum jadwal diisi).
     * $allPeriods = false: hanya kelas pada jadwal periode aktif.
     */
    public function scopeTaughtBy($query, int $teacherId, bool $allPeriods = false)
    {
        return $query->whereIn('classes.id', function ($q) use ($teacherId, $allPeriods) {
            $q->select('class_id')
                ->from('teacher_class')
                ->where('teacher_id', $teacherId)
                ->when(!$allPeriods, fn($qq) => $qq->where('academic_year_id', \App\Models\AcademicYear::activeId()));
        });
    }
}