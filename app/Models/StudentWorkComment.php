<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentWorkComment extends Model
{
    protected $fillable = ['student_work_id', 'parent_id', 'commenter_name', 'content', 'image_path'];

    // Hanya komentar induk (bukan balasan) — dipakai untuk list utama
    public function replies(): HasMany
    {
        return $this->hasMany(StudentWorkComment::class, 'parent_id')->oldest();
    }

    public function parent()
    {
        return $this->belongsTo(StudentWorkComment::class, 'parent_id');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}