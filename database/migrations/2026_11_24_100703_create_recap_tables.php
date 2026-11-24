<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asas_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedSmallInteger('score');
            $table->timestamps();
            $table->unique(['class_id', 'student_id']);
        });

        Schema::create('recap_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('material_topic_id')->nullable(); // null = bobot ASAS
            $table->decimal('weight', 6, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recap_weights');
        Schema::dropIfExists('asas_scores');
    }
};