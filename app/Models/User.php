<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'otp_code',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'otp_code',
        'remember_token',
    ];
}
