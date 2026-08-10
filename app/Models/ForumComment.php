<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumComment extends Model
{
    protected $fillable = ['forum_post_id', 'parent_id', 'commenter_name', 'content', 'image_path'];

    // Balasan langsung (1 tingkat)
    public function replies(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->oldest();
    }

    // Balasan rekursif (semua tingkat, untuk hitung total & render nested)
    public function repliesRecursive(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->with('repliesRecursive')->oldest();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(ForumLike::class, 'likeable');
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