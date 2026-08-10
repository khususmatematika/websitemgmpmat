<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_work_comments', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content');
        });
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('student_work_comments', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
        Schema::table('forum_comments', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};