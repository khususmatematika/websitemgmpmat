<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_topics', function (Blueprint $table) {
            $table->id();
            $table->enum('jenjang', ['X-E', 'XI-F', 'XII-F', 'XI-F+', 'XII-F+']);
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('title');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('material_topics'); }
};