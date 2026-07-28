<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('classes', 'fase')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('fase', 5)->nullable()->after('jenjang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('classes', 'fase')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('fase');
            });
        }
    }
};