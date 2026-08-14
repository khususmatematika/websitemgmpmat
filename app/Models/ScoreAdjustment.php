<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreAdjustment extends Model
{
    protected $fillable = ['class_id', 'material_topic_id', 'student_id', 'bonus', 'deduction'];
}