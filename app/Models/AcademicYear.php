<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'semester', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static bool $loaded = false;
    protected static ?self $cachedActive = null;

    public static function active(): ?self
    {
        if (!static::$loaded) {
            static::$cachedActive = static::where('is_active', true)->first();
            static::$loaded = true;
        }
        return static::$cachedActive;
    }

    public static function activeId(): ?int
    {
        return static::active()?->id;
    }

    /** Periode yang mencakup tanggal tertentu; bila tidak ada, periode aktif. */
    public static function forDate($date): ?self
    {
        if (!$date) {
            return static::active();
        }

        $found = static::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        return $found ?? static::active();
    }

    public function getLabelAttribute(): string
    {
        return "Semester {$this->semester} {$this->name}";
    }
}