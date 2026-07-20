<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalLesson extends Model
{
    protected $fillable = [
        'title',
        'jenjang',
        'topic',
        'embed_url',
        'uploaded_by_type',
        'uploaded_by_id',
    ];
}