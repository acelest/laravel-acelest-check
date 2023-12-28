<?php
namespace App\Models;

use App\Models\Absence;
use App\Models\Notification;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERVISEUR = 'superviseur';

    protected $fillable = [
        'name', 'email', 'password', 'photo', 'matricule', 'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'full_photo_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->matricule = 'M' . uniqid();
            $user->role = $user->role ?: self::ROLE_USER;

        });
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'user_sender');
    }

    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, 'user_receive');
    }

    public function getFullPhotoPathAttribute()
    {
        return asset('storage/' . $this->photo);
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperviseur()
    {
        return $this->role === self::ROLE_SUPERVISEUR;
    }

}
