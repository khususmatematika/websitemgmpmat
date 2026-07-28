<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentComponent extends Model
{
    protected $fillable = ['teacher_id', 'class_id', 'material_topic_id', 'name', 'weight', 'is_attendance', 'order_index'];

    protected $casts = ['is_attendance' => 'boolean'];

    public function scores(): HasMany
    {
        return $this->hasMany(StudentScore::class);
    }
}