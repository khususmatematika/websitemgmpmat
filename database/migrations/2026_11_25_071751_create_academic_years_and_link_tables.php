<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name', 20);
                $table->enum('semester', ['Ganjil', 'Genap']);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
                $table->unique(['name', 'semester']);
            });
        }

        $tables = ['teaching_journals', 'assessment_components', 'score_adjustments', 'grade_publications', 'asas_scores', 'recap_weights'];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && !Schema::hasColumn($tbl, 'academic_year_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->index();
                });
            }
        }

        // Periode awal otomatis dari tanggal hari ini; semua data lama masuk ke sini
        if (DB::table('academic_years')->count() === 0) {
            $now = now();
            $y = (int) $now->format('Y');
            $m = (int) $now->format('n');

            if ($m >= 7) {
                $name = $y . '/' . ($y + 1); $semester = 'Ganjil';
                $start = "$y-07-01"; $end = ($y + 1) . '-01-31';
            } elseif ($m === 1) {
                $name = ($y - 1) . '/' . $y; $semester = 'Ganjil';
                $start = ($y - 1) . '-07-01'; $end = "$y-01-31";
            } else {
                $name = ($y - 1) . '/' . $y; $semester = 'Genap';
                $start = "$y-02-01"; $end = "$y-06-30";
            }

            DB::table('academic_years')->insert([
                'name' => $name, 'semester' => $semester,
                'start_date' => $start, 'end_date' => $end,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $activeId = DB::table('academic_years')->where('is_active', true)->value('id')
            ?? DB::table('academic_years')->orderByDesc('id')->value('id');

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl)) {
                DB::table($tbl)->whereNull('academic_year_id')->update(['academic_year_id' => $activeId]);
            }
        }

        // Unique index lama (tanpa periode) diganti yang menyertakan periode
        $swap = function (string $table, array $oldCols, string $newName) {
            if (!Schema::hasTable($table)) {
                return;
            }
            try {
                if (!Schema::hasIndex($table, $newName)) {
                    Schema::table($table, function (Blueprint $t) use ($oldCols, $newName) {
                        $t->unique(array_merge(['academic_year_id'], $oldCols), $newName);
                    });
                }

                $plain = $table . '_class_id_idx';
                if (!Schema::hasIndex($table, $plain)) {
                    Schema::table($table, function (Blueprint $t) use ($plain) {
                        $t->index('class_id', $plain);
                    });
                }

                foreach (Schema::getIndexes($table) as $idx) {
                    if (!empty($idx['unique']) && empty($idx['primary']) && $idx['columns'] === $oldCols) {
                        Schema::table($table, function (Blueprint $t) use ($idx) {
                            $t->dropUnique($idx['name']);
                        });
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning("Migrasi unique {$table}: " . $e->getMessage());
            }
        };

        $swap('grade_publications', ['class_id', 'material_topic_id'], 'gp_period_unique');
        $swap('score_adjustments', ['class_id', 'material_topic_id', 'student_id'], 'sa_period_unique');
        $swap('asas_scores', ['class_id', 'student_id'], 'asas_period_unique');
    }

    public function down(): void
    {
        //
    }
};