<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRecord extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['attendance_id', 'break_start', 'break_end'];

    public function attendanceRecord()
    {
        return $this->belongsTo(Attendance::class);
    }
}