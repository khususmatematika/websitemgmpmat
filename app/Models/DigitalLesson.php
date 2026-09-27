<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalLesson extends Model
{
    protected $fillable = [
        'title', 'jenjang', 'topic', 'input_type', 'embed_url', 'embed_code',
        'uploaded_by_type', 'uploaded_by_id',
    ];
}