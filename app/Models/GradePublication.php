<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradePublication extends Model
{
    protected $fillable = ['teacher_id', 'class_id', 'material_topic_id', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];

    public static function isPublished(int $classId, int $topicId): bool
    {
        return self::where('class_id', $classId)
            ->where('material_topic_id', $topicId)
            ->value('is_published') ?? false;
    }
}