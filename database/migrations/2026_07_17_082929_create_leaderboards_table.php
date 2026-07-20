<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_session_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->enum('jenjang', ['X-E', 'XI-F', 'XII-F', 'XI-F+', 'XII-F+']);
            $table->string('topic');
            $table->unsignedTinyInteger('score');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('leaderboards'); }
};