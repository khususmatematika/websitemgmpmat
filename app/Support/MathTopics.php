<?php
namespace App\Support;

class MathTopics
{
    public const TOPICS = [
        'Bilangan',
        'Aljabar dan ',
        'Geometri',
        'Analisis Data dan Peluang',
        'Kalkulus',
    ];

    // Kode => Label. Kode dipakai sebagai value tersimpan di kolom `jenjang`.
    public const JENJANG = [
        'X-E'   => 'Kelas X (Fase E)',
        'XI-F'  => 'Kelas XI (Fase F)',
        'XII-F' => 'Kelas XII (Fase F)',
        'XI-F+' => 'Kelas XI (Fase F+)',
        'XII-F+'=> 'Kelas XII (Fase F+)',
    ];
}