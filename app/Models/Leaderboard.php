<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $fillable = ['quiz_session_id', 'student_name', 'jenjang', 'topic', 'score'];
}