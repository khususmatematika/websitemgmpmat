<?php
namespace App\Console\Commands;

use App\Services\DatabaseBackup;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--keep=30 : Jumlah cadangan otomatis yang disimpan}';
    protected $description = 'Membuat cadangan database portal';

    public function handle(): int
    {
        $path = DatabaseBackup::create('otomatis');
        $this->info('Cadangan dibuat: ' . basename($path));

        // Hapus cadangan otomatis yang lebih lama dari batas simpan (cadangan manual tidak disentuh)
        $files = collect(glob(DatabaseBackup::dir() . '/*_otomatis.sql') ?: [])->sortDesc()->values();
        $files->slice((int) $this->option('keep'))->each(fn($f) => @unlink($f));

        return self::SUCCESS;
    }
}