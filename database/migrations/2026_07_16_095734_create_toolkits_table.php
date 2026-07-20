<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('toolkits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon')->default('calculate'); // nama icon Material Symbols
            $table->text('embed_url'); // URL/embed code (Desmos dsb)
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('toolkits'); }
};