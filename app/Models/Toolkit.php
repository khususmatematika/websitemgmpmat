<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toolkit extends Model
{
    protected $fillable = ['title', 'icon', 'input_type', 'embed_url', 'embed_code'];
}