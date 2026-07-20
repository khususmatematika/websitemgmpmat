<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWorkLike extends Model
{
    protected $fillable = ['student_work_id', 'liker_identifier'];
}