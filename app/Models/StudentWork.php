<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StudentWork extends Model
{
    protected $fillable = [
        'student_name', 'description', 'file_path', 'file_type', 'status', 'rejection_reason',
    ];

    public function likes(): HasMany
    {
        return $this->hasMany(StudentWorkLike::class);
    }

    public function comments(): HasMany
    {
    return $this->hasMany(StudentWorkComment::class)
        ->whereNull('parent_id')
        ->with('replies')
        ->oldest();
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function isLikedBy(string $identifier): bool
    {
        return $this->likes()->where('liker_identifier', $identifier)->exists();
    }
}