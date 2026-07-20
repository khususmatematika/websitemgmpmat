<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTopic extends Model
{
    protected $fillable = ['jenjang', 'semester', 'title', 'order_index'];
}