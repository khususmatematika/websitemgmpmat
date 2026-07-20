<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->string('likeable_type');
            $table->unsignedBigInteger('likeable_id');
            $table->string('liker_identifier');
            $table->timestamps();

            $table->unique(['likeable_type', 'likeable_id', 'liker_identifier'], 'forum_likes_unique');
            $table->index(['likeable_type', 'likeable_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('forum_likes'); }
};