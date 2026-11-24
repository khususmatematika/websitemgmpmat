<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsasScore extends Model
{
    protected $fillable = ['class_id', 'student_id', 'score'];
}