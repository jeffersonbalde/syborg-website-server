<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'tbl_AdminUser';

    protected $fillable = [
        'fullname', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];
}
