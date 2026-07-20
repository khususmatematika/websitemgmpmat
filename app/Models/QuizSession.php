<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    protected $fillable = [
        'student_name', 'class_name', 'jenjang', 'topic',
        'questions', 'answers', 'score', 'status', 'student_identifier',
    ];

    protected $casts = [
        'questions' => 'array',
        'answers' => 'array',
    ];

    // Soal tanpa correct_answer & explanation — aman dikirim ke client
    public function questionsForClient(): array
    {
        return collect($this->questions)->map(function ($q) {
            return [
                'question' => $q['question'],
                'options' => $q['options'],
            ];
        })->toArray();
    }
}