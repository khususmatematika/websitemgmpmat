<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'title',
        'jenjang',
        'semester',
        'file_path',
        'cover_path',
        'file_size',
        'uploaded_by_type',
        'uploaded_by_id',
    ];

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        return $bytes > 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024, 1) . ' KB';
    }

    public function getUploaderNameAttribute(): string
    {
        if ($this->uploaded_by_type === 'teacher') {
            return Teacher::find($this->uploaded_by_id)?->name ?? 'Guru';
        }
        return Admin::find($this->uploaded_by_id)?->name ?? 'Admin';
    }
}