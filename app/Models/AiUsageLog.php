<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = ['identifier', 'feature', 'usage_date', 'count'];

    /**
     * Cek kuota & increment. Return true jika masih dalam batas (boleh lanjut),
     * false jika sudah mencapai limit hari ini.
     */
    public static function attempt(string $identifier, string $feature, int $limit): bool
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $log = self::firstOrCreate(
            ['identifier' => $identifier, 'feature' => $feature, 'usage_date' => $today],
            ['count' => 0]
        );

        if ($log->count >= $limit) {
            return false;
        }

        $log->increment('count');
        return true;
    }

    public static function remaining(string $identifier, string $feature, int $limit): int
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();
        $log = self::where(['identifier' => $identifier, 'feature' => $feature, 'usage_date' => $today])->first();
        return max(0, $limit - ($log->count ?? 0));
    }
}