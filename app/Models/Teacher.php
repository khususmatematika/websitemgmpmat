<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nip', 'name', 'email', 'password', 'photo',
        'whatsapp_number', 'description', 'title',
    ];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_class', 'teacher_id', 'class_id')
            ->withPivot(['day', 'start_time', 'end_time'])
            ->withTimestamps();
    }

    // Nomor WA disanitasi ke format internasional (62xxx, tanpa "+" / "0" ganda)
    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp_number) return null;
        $num = preg_replace('/\D/', '', $this->whatsapp_number);
        if (str_starts_with($num, '0')) $num = '62' . substr($num, 1);
        if (!str_starts_with($num, '62')) $num = '62' . $num;
        return "https://wa.me/{$num}";
    }
}