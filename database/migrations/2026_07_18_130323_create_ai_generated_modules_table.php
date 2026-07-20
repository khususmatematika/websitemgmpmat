<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_generated_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();

            $table->string('school_name');
            $table->string('academic_year');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('fase', 10);
            $table->string('kelas');
            $table->string('mapel')->default('Matematika');
            $table->string('materi');
            $table->unsignedInteger('meetings_count');
            $table->unsignedInteger('duration_minutes');
            $table->string('learning_model')->nullable();
            $table->string('integration')->nullable();
            $table->text('learning_outcomes')->nullable();

            $table->string('teacher_name');
            $table->string('teacher_nip')->nullable();
            $table->string('headmaster_name')->nullable();
            $table->string('headmaster_nip')->nullable();
            $table->string('signing_place')->default('Turen');

            $table->string('reference_file_path')->nullable();

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('batches')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ai_generated_modules'); }
};