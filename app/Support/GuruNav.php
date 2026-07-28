<?php
namespace App\Support;

class GuruNav
{
    public static function items(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => route('guru.dashboard'), 'active' => 'guru.dashboard'],
            ['label' => 'Profil Saya', 'icon' => 'badge', 'url' => route('guru.profile.edit'), 'active' => 'guru.profile.*'],
            ['label' => 'Jurnal Mengajar', 'icon' => 'edit_calendar', 'url' => route('guru.jurnal.index'), 'active' => 'guru.jurnal.*'],
            ['label' => 'Persentase Kehadiran', 'icon' => 'percent', 'url' => route('guru.kehadiran.index'), 'active' => 'guru.kehadiran.*'],
            ['label' => 'Materi', 'icon' => 'book', 'url' => route('guru.materi.index'), 'active' => 'guru.materi.*'],
            ['label' => 'Pembelajaran Digital', 'icon' => 'devices', 'url' => route('guru.pembelajaran-digital.index'), 'active' => 'guru.pembelajaran-digital.*'],
            ['label' => 'Bank Soal', 'icon' => 'quiz', 'url' => route('guru.bank-soal.index'), 'active' => 'guru.bank-soal.*'],
            ['label' => 'Input Nilai', 'icon' => 'grade', 'url' => route('guru.nilai.index'), 'active' => 'guru.nilai.*'],
            ['label' => 'Generator Modul Ajar', 'icon' => 'auto_awesome', 'url' => route('guru.modul-ajar.index'), 'active' => 'guru.modul-ajar.*'],
        ];
    }
}