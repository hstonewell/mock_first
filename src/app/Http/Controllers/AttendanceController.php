<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use PhpParser\Node\Stmt\Break_;

class AttendanceController extends Controller
{
    public function index()
    {
        if(Auth::check()) {
            $attendanceRecord
            = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

            $canClockIn = !$attendanceRecord || ($attendanceRecord && $attendanceRecord->clock_out);

            // セッションフラグを取得
            $isWorking = session('working', false);
            $isClockedOut = session('clockedOut', false);

            return view('index', compact('canClockIn', 'isWorking', 'isClockedOut'));
        }
        return redirect('login');
    }

    //勤務開始
    public function clockIn(Request $request)
    {
        $oldRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        $newRecordDate = Carbon::today()->toDateString();

        if ($oldRecord){
            $oldRecordDate = (new Carbon($oldRecord->date))->toDateString();

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

    //勤務終了
    public function clockOut(Request $request)
    {
        $record = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();

        if (empty($record)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }

        if ($record->clock_out) {
            return redirect()->back()->withErrors(['msg' => 'すでに打刻済みです。']);
        }

        $record->update([
            'clock_out' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '退勤打刻が完了しました。本日もお疲れ様でした。');
    }

    //休憩開始
    public function breakStart()
    {
        $attendanceRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();
        $activeBreak = BreakRecord::where('attendance_id', $attendanceRecord->id)->whereNull('break_end')->first();

        if(empty($attendanceRecord)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }

        if ($activeBreak) {
            return redirect()->back()->withErrors(['msg' => '既に休憩中です。']);
        }

        $breakRecord = BreakRecord::create([
            'attendance_id' => $attendanceRecord->id,
            'break_start' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '休憩を開始しました。');
    }

    //休憩終了
    public function breakEnd()
    {
        $attendanceRecord = Attendance::where('user_id', Auth::id())->orderBy('clock_in', 'desc')->first();
        $breakRecord = BreakRecord::where('attendance_id', $attendanceRecord->id)->whereNull('break_end')->first();

        if(empty($attendanceRecord)) {
            return redirect()->back()->withErrors(['msg' => '出勤打刻がされていません。']);
        }

        if($breakRecord){
            $breakRecord->update([
                'break_end' => Carbon::now(),
            ]);
        return redirect()->back()->with('message', '休憩を終了しました');

        } else {
            return redirect()->back()->withErrors(['msg' => '休憩打刻がされていません。']);
        }

    }

    //日付一覧の表示
    public function viewAttendance(Request $request)
    {
        $today = Carbon::today();
        $date = $request->input('date', $today->toDateString());
        $users = User::all();

        $records = Attendance::with('user', 'breakRecords')->whereDate('date', $today)->paginate(5);

        foreach($records as $record) {
            $record->clock_in = Carbon::parse($record->clock_in)->format('H:i:s');
            $record->clock_out = Carbon::parse($record->clock_out)->format('H:i:s');

            $totalBreakTime = 0;
            foreach ($record->breakRecords as $breakRecord) {
                if ($breakRecord->break_end) {
                    $breakStart = Carbon::parse($breakRecord->break_start);
                    $breakEnd = Carbon::parse($breakRecord->break_end);
                    $totalBreakTime += $breakStart->diffInSeconds($breakEnd);
                }
            }
            $record->total_break = gmdate('H:i:s', $totalBreakTime);

            $actualWorkingTime = Carbon::create();
            if($record->clock_in && $record->clock_out) {
                $clockIn = Carbon::parse($record->clock_in);
                $clockOut = Carbon::parse($record->clock_out);
                $actualWorkingTime = $clockIn->diffInSeconds($clockOut) - $totalBreakTime;
            } else {
                $record->actual_working = '00:00:00';
            }
            $record->actual_working = gmdate('H:i:s', $actualWorkingTime);
        }

        return view('attendance', compact('date', 'users', 'records'));
    }

}
