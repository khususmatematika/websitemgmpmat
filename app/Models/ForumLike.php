<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ForumLike extends Model
{
    protected $fillable = ['likeable_type', 'likeable_id', 'liker_identifier'];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}