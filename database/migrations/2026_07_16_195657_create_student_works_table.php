<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_works', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->enum('file_type', ['image', 'video', 'pdf']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_works'); }
};