<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('actor_type');
        });
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('actor_type');
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', fn (Blueprint $t) => $t->dropColumn('owner_id'));
        Schema::table('forum_comments', fn (Blueprint $t) => $t->dropColumn('owner_id'));
    }
};