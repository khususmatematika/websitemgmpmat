<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'actor_id',
    'actor_type',
    'action',       // <-- Ensure this is whitelisted!
    'description',  // <-- Add this if you plan to log descriptions later
];
}