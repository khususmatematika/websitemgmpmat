<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letterhead extends Model
{
    protected $fillable = [
        'school_name', 'address', 'logo_path', 'headmaster_name', 'headmaster_nip',
        'line1_text', 'line1_size', 'line1_bold',
        'line2_text', 'line2_size', 'line2_bold',
        'line3_text', 'line3_size', 'line3_bold',
        'line4_text', 'line4_size', 'line4_bold',
        'line5_text', 'line5_size', 'line5_bold',
    ];

    protected $casts = [
        'line1_bold' => 'boolean', 'line2_bold' => 'boolean', 'line3_bold' => 'boolean',
        'line4_bold' => 'boolean', 'line5_bold' => 'boolean',
    ];

    public static function current(): self
    {
        $lh = self::firstOrCreate([], ['school_name' => 'SMA Negeri 1 Turen']);

        // Isi default baris kalau masih kosong (pertama kali dipakai)
        if (empty($lh->line1_text)) {
            $lh->update([
                'line1_text' => 'PEMERINTAH PROVINSI JAWA TIMUR', 'line1_size' => 13, 'line1_bold' => true,
                'line2_text' => 'DINAS PENDIDIKAN', 'line2_size' => 13, 'line2_bold' => true,
                'line3_text' => 'SMA NEGERI 1 TUREN', 'line3_size' => 20, 'line3_bold' => true,
                'line4_text' => 'Jalan Mayjend Panjaitan 65 Turen, Malang 65175, Telp (0341) 824711', 'line4_size' => 10, 'line4_bold' => false,
                'line5_text' => 'Laman: www.smanegeri1turen.sch.id, pos-el: admin@smanegeri1turen.sch.id', 'line5_size' => 10, 'line5_bold' => false,
            ]);
        }

        return $lh->fresh();
    }
}