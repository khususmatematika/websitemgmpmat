<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir');
            $table->timestamps();

            $table->unique(['teaching_journal_id', 'student_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('journal_attendances'); }
};