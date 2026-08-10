<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->string('actor_type')->default('student')->after('author_name'); // 'student' | 'teacher'
        });
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->string('actor_type')->default('student')->after('commenter_name');
        });
        Schema::table('student_works', function (Blueprint $table) {
            $table->string('actor_type')->default('student')->after('student_name');
        });
        Schema::table('student_work_comments', function (Blueprint $table) {
            $table->string('actor_type')->default('student')->after('commenter_name');
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', fn (Blueprint $t) => $t->dropColumn('actor_type'));
        Schema::table('forum_comments', fn (Blueprint $t) => $t->dropColumn('actor_type'));
        Schema::table('student_works', fn (Blueprint $t) => $t->dropColumn('actor_type'));
        Schema::table('student_work_comments', fn (Blueprint $t) => $t->dropColumn('actor_type'));
    }
};