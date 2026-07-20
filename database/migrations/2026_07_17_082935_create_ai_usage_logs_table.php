<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // "teacher:5" atau uuid session siswa
            $table->string('feature'); // 'combined_ai' (guru) | 'bank_soal' | 'latihan' (siswa)
            $table->date('usage_date');
            $table->unsignedInteger('count')->default(1);
            $table->timestamps();

            $table->unique(['identifier', 'feature', 'usage_date'], 'ai_usage_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('ai_usage_logs'); }
};