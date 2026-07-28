<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop student_scores dulu (punya FK ke assessment_components)
        Schema::dropIfExists('student_scores');

        // Drop assessment_components (struktur lama tidak lengkap)
        Schema::dropIfExists('assessment_components');

        // Buat ulang assessment_components dengan struktur lengkap
        Schema::create('assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('material_topic_id')->constrained('material_topics')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('weight', 5, 2);
            $table->boolean('is_attendance')->default(false);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });

        // Buat ulang student_scores
        Schema::create('student_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_component_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['assessment_component_id', 'student_id'], 'component_student_unique');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scores');
        Schema::dropIfExists('assessment_components');
    }
};