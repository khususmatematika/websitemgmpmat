<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $table = 'question_bank';

    protected $fillable = [
        'type', 'jenjang', 'topic', 'title', 'file_path',
        'question_text', 'options', 'correct_answer', 'explanation',
        'uploaded_by_type', 'uploaded_by_id',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}