<?php
namespace App\Support;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TeacherSchedule
{
    /**
     * Mengganti seluruh jadwal seorang guru pada periode aktif.
     * $rows: [['class_id' => 1, 'day' => 'Senin', 'start_time' => '07:00', 'end_time' => '08:30'], ...]
     * Jadwal periode lain tidak disentuh.
     */
    public static function replaceForActive(int $teacherId, array $rows): void
    {
        $periodId = AcademicYear::activeId();
        abort_unless($periodId, 422, 'Belum ada tahun ajaran aktif. Hubungi Admin.');

        DB::transaction(function () use ($teacherId, $rows, $periodId) {
            DB::table('teacher_class')
                ->where('teacher_id', $teacherId)
                ->where('academic_year_id', $periodId)
                ->delete();

            $hasTimestamps = Schema::hasColumn('teacher_class', 'created_at');

            foreach ($rows as $r) {
                $row = [
                    'teacher_id' => $teacherId,
                    'class_id' => $r['class_id'],
                    'day' => $r['day'] ?? null,
                    'start_time' => $r['start_time'] ?? null,
                    'end_time' => $r['end_time'] ?? null,
                    'academic_year_id' => $periodId,
                ];
                if ($hasTimestamps) {
                    $row['created_at'] = now();
                    $row['updated_at'] = now();
                }
                DB::table('teacher_class')->insert($row);
            }
        });
    }
}