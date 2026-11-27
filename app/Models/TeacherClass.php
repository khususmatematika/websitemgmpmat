<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherClass extends Model
{
    protected $table = 'teacher_class';

    protected $fillable = ['teacher_id', 'class_id', 'day', 'start_time', 'end_time', 'academic_year_id'];

    /** Semua query jadwal otomatis hanya untuk periode aktif. */
    protected static function booted(): void
    {
        static::addGlobalScope('activePeriod', function (Builder $q) {
            $id = AcademicYear::activeId();
            if ($id) {
                $q->where('teacher_class.academic_year_id', $id);
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}