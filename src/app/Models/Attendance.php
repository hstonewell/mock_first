<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out'];

    public function user()
    {
        /** ユーザー1:出退勤多 */
        return $this->belongsTo(User::class);
    }

    public function breakRecords()
    {
        /** 出退勤1:休憩多 */
        return $this->hasMany(BreakRecord::class);
    }

    public function scopeActiveRecord($query)
    {
        return $query->whereNull('clock_out');
    }

    public function scopeFinishedRecord($query)
    {
        return $query->whereNotNull('clock_out');
    }
}
