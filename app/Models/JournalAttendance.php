<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalAttendance extends Model
{
    protected $fillable = ['teaching_journal_id', 'student_id', 'status'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}