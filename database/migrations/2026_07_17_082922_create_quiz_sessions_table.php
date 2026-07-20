<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('class_name');
            $table->enum('jenjang', ['X-E', 'XI-F', 'XII-F', 'XI-F+', 'XII-F+']);
            $table->string('topic');
            $table->json('questions'); // array 10 soal: {question, options, correct_answer, explanation}
            $table->json('answers')->nullable(); // jawaban siswa setelah selesai
            $table->unsignedTinyInteger('score')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->string('student_identifier'); // session uuid
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('quiz_sessions'); }
};