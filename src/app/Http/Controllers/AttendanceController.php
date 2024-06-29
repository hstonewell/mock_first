<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        if(Auth::check()) {
            return view('index');
        }
    }

    public function clockIn()
    {
        //一番最新のレコードを取り出す
        $oldRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        //最新のレコードがすでに打刻済みの場合の処理
        if($oldRecord){
            $oldRecordClockIn = new Carbon($oldRecord->clockIn);
            $oldRecordDay = $oldRecordClockIn->startOfDay();

            $oldTimeClockOut = new Carbon($oldRecord->clockOut);
            $oldDay = $oldTimeClockOut->startOfDay();

            $newRecordDay = Carbon::today();

            if (($oldRecordDay == $newRecordDay || $oldDay == $newRecordDay) && empty($oldRecord->clock_out)) {
                return redirect()->back()->withErrors(['msg' => 'すでに打刻済みです。']);
            }
        }
            //0時を超えたら新しいレコード
            $record = Attendance::create([
                'user_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'clock_in' => Carbon::now(),
            ]);
            return redirect()->back()->with('message', '打刻が完了しました。');
    }

    public function clockOut()
    {
        $record = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        if ($record) {
            $record->update([
                'clock_out' => Carbon::now(),
            ]);
            return redirect()->back()->with('message', '退勤打刻が完了しました。本日もお疲れ様でした。');
        } else {
            return redirect()->back()->withErrors(['msg' => '退勤打刻を行うための出勤打刻が存在しません。']);
        }
    }

    public function breakStart()
    {
        $attendanceRecord = Attendance::where('user_id', Auth::id())
        ->orderBy('clock_in', 'desc')
        ->first();

        $breakRecord = BreakRecord::create([
            'attendance_id' => $attendanceRecord->id,
            'break_start' => Carbon::now(),
        ]);
        return redirect()->back();
        //エラーメッセージを追加
    }

    public function breakEnd()
    {
        $attendanceRecord = Attendance::where('user_id', Auth::id())
        ->orderBy('clock_in', 'desc')
        ->first();

        $breakRecord = BreakRecord::where('attendance_id', $attendanceRecord->id)
        ->whereNull('break_end')
        ->first();

        if($breakRecord){
            $breakRecord->update([
                'break_end' => Carbon::now(),
            ]);
        return redirect()->back();
            //打刻完了メッセージを追加
        } else {
            return redirect()->back();
            //エラーメッセージを追加。出勤打刻が存在しない場合。
        }
    }
}
