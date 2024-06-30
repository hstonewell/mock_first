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

        $oldRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        $newRecordDate = Carbon::today()->toDateString();

        //最新レコードの打刻日時と今日の日付の比較
        if ($oldRecord){
            $oldRecordDate = (new Carbon($oldRecord->date))->toDateString();

            //最新のレコードがすでに打刻済みの場合の処理
            if ($oldRecordDate == $newRecordDate && empty($oldRecord->clock_out)) {
                return redirect()->back()->withErrors(['msg' => 'すでに打刻済みです。']);
            }
        }

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

        //レコードが存在しない場合
        if (empty($record)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }
        //すでに退勤打刻がされている場合
        if ($record->clock_out) {
            return redirect()->back()->withErrors(['msg' => 'すでに打刻済みです。']);
        }

        $record->update([
            'clock_out' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '退勤打刻が完了しました。本日もお疲れ様でした。');
    }

    public function breakStart()
    {
        $attendanceRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        if(empty($attendanceRecord)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }
        //現在取っている休憩があるかどうか
        $activeBreak = BreakRecord::where('attendance_id', $attendanceRecord->id)->whereNull('break_end')->first();
        if ($activeBreak) {
            return redirect()->back()->withErrors(['msg' => '既に休憩中です。']);
        }

        $breakRecord = BreakRecord::create([
            'attendance_id' => $attendanceRecord->id,
            'break_start' => Carbon::now(),
        ]);
        return redirect()->back()->with('message', '休憩を開始しました。');
    }

    public function breakEnd()
    {
        //最新の出勤記録を取得する
        $attendanceRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        //出勤記録がない場合
        if(empty($attendanceRecord)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }
        $breakRecord = BreakRecord::where('attendance_id', $attendanceRecord->id)->whereNull('break_end')->first();

        if($breakRecord){
            $breakRecord->update([
                'break_end' => Carbon::now(),
            ]);
        return redirect()->back()->with('message', '休憩を終了しました');

        } else {
            return redirect()->back()->withErrors(['msg' => '休憩打刻がされていません。']);
        }
    }
}
