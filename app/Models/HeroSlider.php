<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlider extends Model
{
    protected $table = 'tbl_HeroSlider';

    protected $fillable = [
        'title', 'description', 'content', "image"
    ];
}
