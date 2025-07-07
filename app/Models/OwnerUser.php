<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OwnerUser extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'tbl_OwnerUser';

    protected $fillable = ['fullname', 'email', 'password'];

    protected $hidden = ['password'];
}
