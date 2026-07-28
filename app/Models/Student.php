<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['nis', 'name', 'password'];
    protected $hidden = ['password'];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
            ->withTimestamps();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StudentScore::class);
    }

    public function checkPassword(string $input): bool
    {
        if (empty($this->password)) {
            return $this->nis && $input === $this->nis;
        }
        return \Illuminate\Support\Facades\Hash::check($input, $this->password);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? bcrypt($value) : null;
    }
}