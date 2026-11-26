<?php
namespace App\Support;

class SemesterHelper
{
    /** Nomor semester 1-6 dari jenjang kelas (X/XI/XII) dan semester periode (Ganjil/Genap). */
    public static function number(?string $jenjang, ?string $semester): ?int
    {
        $map = ['X' => 1, '10' => 1, 'XI' => 2, '11' => 2, 'XII' => 3, '12' => 3];
        $level = $map[strtoupper(trim((string) $jenjang))] ?? null;

        if (!$level || !in_array($semester, ['Ganjil', 'Genap'], true)) {
            return null;
        }

        return ($level - 1) * 2 + ($semester === 'Ganjil' ? 1 : 2);
    }

    /** Kelas pilihan / Mat Lanjut: fase F+ atau nama kelas memuat kata "lanjut". */
    public static function isAdvanced($class): bool
    {
        if (strtoupper(trim((string) ($class->fase ?? ''))) === 'F+') {
            return true;
        }

        return str_contains(mb_strtolower((string) ($class->name ?? '')), 'lanjut');
    }

    public static function track($class): string
    {
        return self::isAdvanced($class) ? 'lanjut' : 'reguler';
    }

    /** Semester pertama pembelajaran: Mat Lanjut mulai semester 3. */
    public static function firstSemester($class): int
    {
        return self::isAdvanced($class) ? 3 : 1;
    }

    /** Daftar semester sebelumnya yang perlu diisi untuk kelas ini pada semester berjalan. */
    public static function previousSemesters($class, ?int $current): array
    {
        if (!$current) {
            return [];
        }

        $first = self::firstSemester($class);

        return $current > $first ? range($first, $current - 1) : [];
    }

    public static function label(int $n): string
    {
        $names = ['X', 'XI', 'XII'];
        $level = $names[intdiv($n - 1, 2)] ?? '?';

        return "Semester {$n} (Kelas {$level} " . ($n % 2 === 1 ? 'Ganjil' : 'Genap') . ')';
    }
}