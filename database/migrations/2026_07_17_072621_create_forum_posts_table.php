<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('forum_posts'); }
};