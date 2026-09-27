<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('digital_lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('digital_lessons', 'input_type')) {
                $table->enum('input_type', ['url', 'code'])->default('url')->after('topic');
            }
            if (!Schema::hasColumn('digital_lessons', 'embed_code')) {
                $table->text('embed_code')->nullable()->after('embed_url');
            }
        });

        Schema::table('digital_lessons', function (Blueprint $table) {
            $table->string('embed_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('digital_lessons', function (Blueprint $table) {
            $table->dropColumn(['input_type', 'embed_code']);
        });
    }
};