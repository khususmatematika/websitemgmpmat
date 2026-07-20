<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->string('reporter_name')->nullable();
            $table->string('reason'); // Tidak Pantas, Spam, dll
            $table->enum('status', ['pending', 'reviewed'])->default('pending');
            $table->text('admin_action')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('reports'); }
};