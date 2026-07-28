<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('student_scores')) {
            Schema::create('student_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assessment_component_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->decimal('score', 5, 2)->nullable();
                $table->timestamps();

                $table->unique(['assessment_component_id', 'student_id'], 'component_student_unique');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('student_scores'); }
};