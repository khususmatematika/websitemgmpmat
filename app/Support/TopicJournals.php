<?php
namespace App\Support;

use App\Models\TeachingJournal;

class TopicJournals
{
    protected static function norm(?string $s): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim((string) $s)));
    }

    /** ID jurnal yang materinya sama dengan topik (spasi dan huruf besar-kecil diabaikan). */
    public static function ids(int $classId, $topic, ?int $periodId = null, ?int $teacherId = null)
    {
        $target = self::norm($topic->title);

        return TeachingJournal::where('class_id', $classId)
            ->when($periodId, fn($q) => $q->where('academic_year_id', $periodId))
            ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
            ->get(['id', 'materi'])
            ->filter(fn($j) => self::norm($j->materi) === $target)
            ->pluck('id');
    }
}