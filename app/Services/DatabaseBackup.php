<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseBackup
{
    public const MARKER = '-- MATEMATIKA-BACKUP v1';

    /** Tabel sementara: hanya struktur, tanpa isi. */
    protected const SKIP_DATA = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    public static function dir(): string
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function create(string $label = 'manual'): string
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $pdo = DB::getPdo();
        $label = preg_replace('/[^a-z]/', '', strtolower($label)) ?: 'manual';
        $path = self::dir() . DIRECTORY_SEPARATOR . 'backup_' . now()->format('Y-m-d_His') . '_' . $label . '.sql';

        $fh = fopen($path, 'w');
        if (!$fh) {
            throw new \RuntimeException('Tidak bisa menulis file cadangan. Cek izin folder storage/app/backups.');
        }

        fwrite($fh, self::MARKER . "\n");
        fwrite($fh, '-- Database: ' . DB::getDatabaseName() . "\n");
        fwrite($fh, '-- Dibuat: ' . now()->format('Y-m-d H:i:s') . "\n");
        fwrite($fh, "SET NAMES utf8mb4;\n");
        fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");

        foreach (DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'") as $row) {
            $table = array_values((array) $row)[0];
            $create = (array) DB::select("SHOW CREATE TABLE `{$table}`")[0];
            $ddl = preg_replace('/\s*\n\s*/', ' ', $create['Create Table']);

            if (in_array($table, self::SKIP_DATA, true)) {
                fwrite($fh, preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $ddl) . ";\n");
                continue;
            }

            fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($fh, $ddl . ";\n");

            $batch = [];
            $cols = null;

            foreach (DB::table($table)->cursor() as $r) {
                $arr = (array) $r;
                if ($cols === null) {
                    $cols = '`' . implode('`,`', array_keys($arr)) . '`';
                }
                $batch[] = '(' . implode(',', array_map(fn($v) => self::val($pdo, $v), array_values($arr))) . ')';

                if (count($batch) >= 100) {
                    fwrite($fh, "INSERT INTO `{$table}` ({$cols}) VALUES " . implode(',', $batch) . ";\n");
                    $batch = [];
                }
            }

            if ($batch) {
                fwrite($fh, "INSERT INTO `{$table}` ({$cols}) VALUES " . implode(',', $batch) . ";\n");
            }
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);

        return $path;
    }

    protected static function val($pdo, $v): string
    {
        if ($v === null) return 'NULL';
        if (is_bool($v)) return $v ? '1' : '0';
        if (is_int($v) || is_float($v)) return (string) $v;
        return $pdo->quote((string) $v);
    }

    public static function isValid(string $path): bool
    {
        $fh = @fopen($path, 'r');
        if (!$fh) return false;
        $first = trim((string) fgets($fh));
        fclose($fh);
        return str_starts_with($first, self::MARKER);
    }

    /** Memulihkan database dari file cadangan. Mengembalikan jumlah perintah yang dijalankan. */
    public static function restore(string $path): int
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        if (!self::isValid($path)) {
            throw new \RuntimeException('File bukan cadangan yang dibuat oleh portal ini.');
        }

        $fh = fopen($path, 'r');
        $count = 0;

        try {
            while (($line = fgets($fh)) !== false) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '--')) {
                    continue;
                }
                DB::unprepared($line);
                $count++;
            }
        } finally {
            fclose($fh);
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
        }

        return $count;
    }
}