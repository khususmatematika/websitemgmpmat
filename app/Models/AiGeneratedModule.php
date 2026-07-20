<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneratedModule extends Model
{
    protected $fillable = [
    'teacher_id', 'school_name', 'academic_year', 'semester', 'fase', 'kelas',
    'mapel', 'materi', 'meetings_count', 'completed_meetings', 'duration_minutes',
    'learning_model', 'integration', 'learning_outcomes', 'teacher_name', 'teacher_nip',
    'headmaster_name', 'headmaster_nip', 'signing_place', 'reference_file_path',
    'status', 'batches', 'topic_map', 'error_message',
    ];

    protected $casts = [
        'batches' => 'array',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getDurationLabelAttribute(): string
{
    return "{$this->meetings_count} (2x45 menit)";
}

public function getMeetingsPerBatchAttribute(): int
{
    return 3;
}

public function getTotalBatchesAttribute(): int
{
    return max(1, (int) ceil($this->meetings_count / 3));
}
}