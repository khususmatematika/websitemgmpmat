<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ai_generated_modules', function (Blueprint $table) {
            $table->unsignedInteger('completed_meetings')->default(0)->after('meetings_count');
            $table->text('topic_map')->nullable()->after('batches');
        });
    }

    public function down(): void
    {
        Schema::table('ai_generated_modules', function (Blueprint $table) {
            $table->dropColumn(['completed_meetings', 'topic_map']);
        });
    }
};