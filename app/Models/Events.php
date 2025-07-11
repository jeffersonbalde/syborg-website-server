<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'tbl_Events';

    protected $fillable = ['title', 'location', 'event_date', 'start_time', 'end_time'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, "event_id");
    }
}
