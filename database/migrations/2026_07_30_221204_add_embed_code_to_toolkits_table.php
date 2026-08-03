<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('toolkits', function (Blueprint $table) {
            $table->text('embed_code')->nullable()->after('embed_url');
        });
    }
    public function down(): void
    {
        Schema::table('toolkits', function (Blueprint $table) {
            $table->dropColumn('embed_code');
        });
    }
};