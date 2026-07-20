<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letterhead extends Model
{
    protected $fillable = [
        'school_name', 'address', 'logo_path', 'headmaster_name', 'headmaster_nip',
    ];

    // Selalu ambil/berikan 1 baris konfigurasi tunggal
    public static function current(): self
    {
        return self::firstOrCreate([], [
            'school_name' => 'SMA Negeri 1 Turen',
        ]);
    }
}