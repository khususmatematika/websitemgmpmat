<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviousSemesterGrade extends Model
{
    protected $fillable = ['student_id', 'track', 'semester_number', 'score'];
}