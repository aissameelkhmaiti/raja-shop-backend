<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'avatar',
        'otp_code',          // <-- ajouté
        'otp_expires_at',    // <-- ajouté
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime', // <-- cast en datetime pour Carbon
    ];


            public function getAvatarUrlAttribute()
        {
            return $this->avatar
                ? asset('storage/' . $this->avatar)
                : asset('storage/avatars/default.png');
        }

        protected $appends = ['avatar_url'];
 

    

    
}
