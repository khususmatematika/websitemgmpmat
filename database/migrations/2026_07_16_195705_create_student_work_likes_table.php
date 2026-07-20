<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_work_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_work_id')->constrained()->cascadeOnDelete();
            $table->string('liker_identifier'); // browser session identifier
            $table->timestamps();
            $table->unique(['student_work_id', 'liker_identifier']);
        });
    }
    public function down(): void { Schema::dropIfExists('student_work_likes'); }
};