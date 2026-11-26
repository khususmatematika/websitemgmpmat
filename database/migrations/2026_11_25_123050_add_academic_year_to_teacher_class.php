<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('teacher_class', 'academic_year_id')) {
            Schema::table('teacher_class', function (Blueprint $table) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            });
        }

        $activeId = DB::table('academic_years')->where('is_active', true)->value('id')
            ?? DB::table('academic_years')->orderByDesc('id')->value('id');

        if ($activeId) {
            DB::table('teacher_class')->whereNull('academic_year_id')->update(['academic_year_id' => $activeId]);
        }

        // Unique lama (tanpa periode) akan menghalangi jadwal yang sama di semester berikutnya
        try {
            foreach (Schema::getIndexes('teacher_class') as $idx) {
                if (!empty($idx['unique']) && empty($idx['primary']) && !in_array('academic_year_id', $idx['columns'])) {
                    Schema::table('teacher_class', function (Blueprint $t) use ($idx) {
                        $t->dropUnique($idx['name']);
                    });
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Migrasi teacher_class unique: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        //
    }
};