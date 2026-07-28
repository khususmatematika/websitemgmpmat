<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('assessment_components')) {
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
        }
    }
    public function down(): void { Schema::dropIfExists('assessment_components'); }
};