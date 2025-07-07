<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StudentUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'tbl_StudentUser';

    protected $fillable = [
        'edp_number', 'firstname', 'middlename', 'lastname',
        'course', 'year_level', 'status', 'gender', 'age',
        'birthday', 'contact_number', 'email', 'password', 'profile_picture',
    ];

    protected $hidden = ['password'];
}
