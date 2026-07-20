<?php
namespace App\Support;

class AdminNav
{
    public static function items(): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => route('admin.dashboard'), 'active' => 'admin.dashboard'],
            ['label' => 'Data Guru', 'icon' => 'badge', 'url' => route('admin.guru.index'), 'active' => 'admin.guru.*'],
            ['label' => 'Data Siswa', 'icon' => 'groups', 'url' => route('admin.siswa.index'), 'active' => 'admin.siswa.*'],
            ['label' => 'Data Kelas', 'icon' => 'meeting_room', 'url' => route('admin.kelas.index'), 'active' => 'admin.kelas.*'],
            ['label' => 'Materi', 'icon' => 'book', 'url' => route('admin.materi.index'), 'active' => 'admin.materi.*'],
            ['label' => 'Topik Kurikulum', 'icon' => 'list_alt', 'url' => route('admin.topik-materi.index'), 'active' => 'admin.topik-materi.*'],
            ['label' => 'Pembelajaran Digital', 'icon' => 'devices', 'url' => route('admin.pembelajaran-digital.index'), 'active' => 'admin.pembelajaran-digital.*'],
            ['label' => 'Toolkit', 'icon' => 'calculate', 'url' => route('admin.toolkit.index'), 'active' => 'admin.toolkit.*'],
            ['label' => 'Karya Siswa', 'icon' => 'auto_awesome_motion', 'url' => route('admin.karya-siswa.index'), 'active' => 'admin.karya-siswa.*'],
            ['label' => 'Laporan', 'icon' => 'flag', 'url' => route('admin.laporan.index'), 'active' => 'admin.laporan.*'],
            ['label' => 'Bank Soal', 'icon' => 'quiz', 'url' => route('admin.bank-soal.index'), 'active' => 'admin.bank-soal.*'],
            ['label' => 'Kop Surat', 'icon' => 'article', 'url' => route('admin.letterhead.edit'), 'active' => 'admin.letterhead.*'],
        ];
    }
}