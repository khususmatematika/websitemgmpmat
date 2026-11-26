<?php
namespace App\Support;

use Illuminate\Support\Facades\Route;

class AdminNav
{
    public static function items(): array
    {
        $menu = [
            ['Dashboard', 'dashboard', 'admin.dashboard', 'admin.dashboard'],
            ['Data Guru', 'badge', 'admin.guru.index', 'admin.guru.*'],
            ['Data Siswa', 'groups', 'admin.siswa.index', 'admin.siswa.*'],
            ['Data Kelas', 'meeting_room', 'admin.kelas.index', 'admin.kelas.*'],
            ['Tahun Ajaran', 'event_repeat', 'admin.tahun-ajaran.index', 'admin.tahun-ajaran.*'],
            ['Materi', 'book', 'admin.materi.index', 'admin.materi.*'],
            ['Topik Kurikulum', 'list_alt', 'admin.topik-materi.index', 'admin.topik-materi.*'],
            ['Pembelajaran Digital', 'devices', 'admin.pembelajaran-digital.index', 'admin.pembelajaran-digital.*'],
            ['Toolkit', 'calculate', 'admin.toolkit.index', 'admin.toolkit.*'],
            ['Karya Siswa', 'auto_awesome_motion', 'admin.karya-siswa.index', 'admin.karya-siswa.*'],
            ['Laporan', 'flag', 'admin.laporan.index', 'admin.laporan.*'],
            ['Bank Soal', 'quiz', 'admin.bank-soal.index', 'admin.bank-soal.*'],
            ['Backup Data', 'cloud_download', 'admin.backup.index', 'admin.backup.*'],
            ['Kop Surat', 'article', 'admin.letterhead.edit', 'admin.letterhead.*'],
        ];

        $items = [];
        foreach ($menu as [$label, $icon, $route, $active]) {
            // Lewati item bila route-nya belum terdaftar, supaya panel tidak error
            if (!Route::has($route)) {
                continue;
            }
            $items[] = [
                'label' => $label,
                'icon' => $icon,
                'url' => route($route),
                'active' => $active,
            ];
        }

        return $items;
    }
}