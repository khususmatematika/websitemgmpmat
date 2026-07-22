<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan tidak ada unique constraint yang membatasi 1 baris per teacher_id+class_id
        // (tabel dari Modul 1 sudah tanpa unique constraint semacam itu by default, migration ini jaga-jaga)
    }

    public function down(): void {}
};