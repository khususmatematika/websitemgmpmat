<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('toolkits', function (Blueprint $table) {
            $table->enum('input_type', ['url', 'code'])->default('url')->after('icon');
            $table->string('embed_url')->nullable()->change();
        });
    }
    public function down(): void
    {
        Schema::table('toolkits', function (Blueprint $table) {
            $table->dropColumn('input_type');
        });
    }
};