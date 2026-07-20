<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()
                ->constrained('forum_comments')->cascadeOnDelete();
            $table->string('commenter_name');
            $table->text('content');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('forum_comments'); }
};