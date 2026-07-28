<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    protected $table = 'classes';
    protected $fillable = ['name', 'jenjang', 'fase', 'class_type'];

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class', 'class_id', 'teacher_id')
            ->withPivot(['day', 'start_time', 'end_time'])
            ->withTimestamps();
    }

    public function students()
    {
    return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')->withTimestamps();
    }
}