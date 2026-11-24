<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecapWeight extends Model
{
    protected $fillable = ['class_id', 'material_topic_id', 'weight'];
}