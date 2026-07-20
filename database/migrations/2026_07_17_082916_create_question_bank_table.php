<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['file', 'ai_question']);
            $table->enum('jenjang', ['X-E', 'XI-F', 'XII-F', 'XI-F+', 'XII-F+']);
            $table->string('topic');

            // Untuk type = file (upload guru/admin)
            $table->string('title')->nullable();
            $table->string('file_path')->nullable();

            // Untuk type = ai_question (hasil generate, disimpan untuk reuse)
            $table->text('question_text')->nullable();
            $table->json('options')->nullable(); // {"A": "...", "B": "...", "C": "...", "D": "..."}
            $table->string('correct_answer', 5)->nullable(); // "A" / "B" / "C" / "D"
            $table->text('explanation')->nullable();

            $table->string('uploaded_by_type')->nullable(); // 'teacher' | 'admin' | 'student'
            $table->unsignedBigInteger('uploaded_by_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('question_bank'); }
};