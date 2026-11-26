<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradePublication extends Model
{
    protected $fillable = ['teacher_id', 'class_id', 'material_topic_id', 'academic_year_id', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];

    public static function isPublished(int $classId, int $topicId, ?int $periodId = null): bool
    {
        $periodId = $periodId ?? AcademicYear::activeId();

        return (bool) self::where('class_id', $classId)
            ->where('material_topic_id', $topicId)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->value('is_published');
    }
}