<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumPost extends Model
{
    protected $fillable = ['author_name', 'content', 'image_path'];

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class)
            ->whereNull('parent_id')
            ->with('repliesRecursive')
            ->oldest();
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

    public function totalCommentCount(): int
    {
        return $this->comments->sum(fn($c) => 1 + $c->repliesRecursive->count());
    }
}