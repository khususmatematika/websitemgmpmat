<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('journal_date');
            $table->string('materi')->nullable();
            $table->text('kegiatan')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'journal_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('teaching_journals'); }
};