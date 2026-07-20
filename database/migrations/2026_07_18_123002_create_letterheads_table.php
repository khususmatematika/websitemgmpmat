<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letterheads', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMA Negeri 1 Turen');
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('headmaster_name')->nullable();
            $table->string('headmaster_nip')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('letterheads'); }
};