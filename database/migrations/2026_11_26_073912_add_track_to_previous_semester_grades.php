<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('previous_semester_grades', 'track')) {
            Schema::table('previous_semester_grades', function (Blueprint $table) {
                $table->string('track', 10)->default('reguler')->after('student_id');
            });
        }

        // Unique baru dibuat dulu (menyertakan jalur), baru yang lama dihapus
        if (!Schema::hasIndex('previous_semester_grades', 'psg_track_unique')) {
            Schema::table('previous_semester_grades', function (Blueprint $table) {
                $table->unique(['student_id', 'track', 'semester_number'], 'psg_track_unique');
            });
        }

        foreach (Schema::getIndexes('previous_semester_grades') as $idx) {
            if (!empty($idx['unique']) && empty($idx['primary']) && $idx['columns'] === ['student_id', 'semester_number']) {
                Schema::table('previous_semester_grades', function (Blueprint $table) use ($idx) {
                    $table->dropUnique($idx['name']);
                });
            }
        }
    }

    public function down(): void
    {
        //
    }
};