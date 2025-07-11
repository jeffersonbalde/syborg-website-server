<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StudentUser extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'tbl_StudentUser';

    protected $fillable = [
        'edp_number', 'firstname', 'middlename', 'lastname',
        'course', 'year_level', 'status', 'gender', 'age',
        'birthday', 'contact_number', 'email', 'password', 'profile_picture', 'active_status', 'qr_code',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'edp_number', 'edp_number');
    }

    protected $hidden = ['password'];
}
