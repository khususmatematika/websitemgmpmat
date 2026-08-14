<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('score_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('material_topic_id')->constrained('material_topics')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('bonus', 5, 2)->default(0);
            $table->decimal('deduction', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['class_id', 'material_topic_id', 'student_id'], 'score_adj_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('score_adjustments'); }
};