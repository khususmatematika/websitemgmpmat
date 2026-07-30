<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grade_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('material_topic_id')->constrained('material_topics')->cascadeOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['class_id', 'material_topic_id'], 'class_topic_publish_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('grade_publications'); }
};