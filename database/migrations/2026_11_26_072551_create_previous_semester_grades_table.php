<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('previous_semester_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('semester_number'); // 1-6
            $table->unsignedTinyInteger('score');           // 0-100
            $table->timestamps();
            $table->unique(['student_id', 'semester_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('previous_semester_grades');
    }
};