<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'tbl_Attendance';

    protected $fillable = ['event_id', 'edp_number', 'time_in', 'time_out', 'present', 'notes',];

    public function event()
    {
        return $this->belongsTo(Events::class, "event_id");
    }

    public function student()
    {
        return $this->belongsTo(StudentUser::class, 'edp_number', 'edp_number');
    }
}
