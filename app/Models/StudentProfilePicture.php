<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfilePicture extends Model
{
    protected $table = 'tbl_StudentProfilePicture';

    protected $fillable = ['name'];
}
