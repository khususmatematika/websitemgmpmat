<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = ['reportable_type', 'reportable_id', 'reporter_name', 'reason', 'status', 'admin_action'];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public const REASONS = [
        'Tidak Pantas',
        'Spam/Iklan',
        'Pelanggaran Hak Cipta',
        'Perundungan/Bullying',
        'Lainnya',
    ];
}