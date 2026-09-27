<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('digital_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('digital_lessons', 'uploaded_by_type')) {
                $table->string('uploaded_by_type')->nullable()->change();
            }
            if (Schema::hasColumn('digital_lessons', 'uploaded_by_id')) {
                $table->unsignedBigInteger('uploaded_by_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        //
    }
};