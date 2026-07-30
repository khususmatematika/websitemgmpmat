<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('letterheads', function (Blueprint $table) {
            if (!Schema::hasColumn('letterheads', 'line1_text')) $table->string('line1_text')->nullable()->after('logo_path');
            if (!Schema::hasColumn('letterheads', 'line1_size')) $table->unsignedInteger('line1_size')->default(13)->after('line1_text');
            if (!Schema::hasColumn('letterheads', 'line1_bold')) $table->boolean('line1_bold')->default(true)->after('line1_size');

            if (!Schema::hasColumn('letterheads', 'line2_text')) $table->string('line2_text')->nullable()->after('line1_bold');
            if (!Schema::hasColumn('letterheads', 'line2_size')) $table->unsignedInteger('line2_size')->default(13)->after('line2_text');
            if (!Schema::hasColumn('letterheads', 'line2_bold')) $table->boolean('line2_bold')->default(true)->after('line2_size');

            if (!Schema::hasColumn('letterheads', 'line3_text')) $table->string('line3_text')->nullable()->after('line2_bold');
            if (!Schema::hasColumn('letterheads', 'line3_size')) $table->unsignedInteger('line3_size')->default(20)->after('line3_text');
            if (!Schema::hasColumn('letterheads', 'line3_bold')) $table->boolean('line3_bold')->default(true)->after('line3_size');

            if (!Schema::hasColumn('letterheads', 'line4_text')) $table->string('line4_text')->nullable()->after('line3_bold');
            if (!Schema::hasColumn('letterheads', 'line4_size')) $table->unsignedInteger('line4_size')->default(10)->after('line4_text');
            if (!Schema::hasColumn('letterheads', 'line4_bold')) $table->boolean('line4_bold')->default(false)->after('line4_size');

            if (!Schema::hasColumn('letterheads', 'line5_text')) $table->string('line5_text')->nullable()->after('line4_bold');
            if (!Schema::hasColumn('letterheads', 'line5_size')) $table->unsignedInteger('line5_size')->default(10)->after('line5_text');
            if (!Schema::hasColumn('letterheads', 'line5_bold')) $table->boolean('line5_bold')->default(false)->after('line5_size');
        });
    }

    public function down(): void
    {
        Schema::table('letterheads', function (Blueprint $table) {
            $table->dropColumn([
                'line1_text', 'line1_size', 'line1_bold',
                'line2_text', 'line2_size', 'line2_bold',
                'line3_text', 'line3_size', 'line3_bold',
                'line4_text', 'line4_size', 'line4_bold',
                'line5_text', 'line5_size', 'line5_bold',
            ]);
        });
    }
};