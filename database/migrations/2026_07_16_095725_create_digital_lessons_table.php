<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('digital_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('jenjang', ['X-E', 'XI-F', 'XII-F', 'XI-F+', 'XII-F+']);
            $table->string('topic');
            $table->text('embed_url');
            $table->string('uploaded_by_type');
            $table->unsignedBigInteger('uploaded_by_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_lessons');
    }
};