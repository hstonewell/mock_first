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
            $attendance
            = Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();

            $latestBreak = null;
            if ($attendance) {
                $latestBreak = BreakRecord::where('attendance_id', $attendance->id)
                    ->whereNull('break_end')
                    ->first();
            }

            return view('index', compact('attendance', 'latestBreak'));
        }
        return redirect('login');
    }

    //勤務開始
    public function clockIn(Request $request)
    {
        $activeAttendance =
        Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if (empty($activeAttendance)) {
            Attendance::create([
            'user_id' => Auth::id(),
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now(),
        ]);
        } else {
            return redirect()->back()->withErrors(['msg' => 'すでに本日の出勤記録があります']);
        }

        return redirect()->back()->with('message', '出勤打刻が完了しました。');
    }

    //勤務終了
    public function clockOut(Request $request)
    {
        $activeAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($activeAttendance) {
            $activeAttendance->update(['clock_out' => Carbon::now()]);
        } else {
            $prevAttendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', now()->subDay()->toDateString())
                ->first();

            if ($prevAttendance && is_null($prevAttendance->clock_out)) {
                $prevAttendance->update(['clock_out' => Carbon::now()->endOfDay()]);
            }

            Attendance::create([
                'user_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'clock_in' => Carbon::now()->startOfDay(),
                'clock_out' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('message', '退勤打刻が完了しました。本日もお疲れ様でした。');
    }

    //休憩開始
    public function breakStart()
    {
        $activeAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($activeAttendance) {
            BreakRecord::create([
                'attendance_id' => $activeAttendance->id,
                'break_start' => Carbon::now(),
            ]);
        } else {
            $prevAttendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', now()->subDay()->toDateString())
                ->first();

            if ($prevAttendance && is_null($prevAttendance->clock_out)) {
                $prevAttendance->update(['clock_out' => Carbon::now()->endOfDay()]);
            }

            $newAttendance = Attendance::create([
                'user_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'clock_in' => Carbon::now()->startOfDay(),
            ]);

            BreakRecord::create([
                'attendance_id' => $newAttendance->id,
                'break_start' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('message', '休憩を開始しました。');
    }

    //休憩終了
    public function breakEnd()
    {
        $activeAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($activeAttendance) {
            $breakRecord = BreakRecord::where('attendance_id', $activeAttendance->id)
                ->whereNull('break_end')
                ->first();

            if ($breakRecord) {
                if (Carbon::now()->format('H:i') == '00:00') {
                    $breakRecord->update(['break_end' => Carbon::now()]);
                } else {
                    // 0時をまたいだ場合
                    $breakRecord->update(['break_end' => Carbon::now()->startOfDay()]);

                    // 新しい出勤記録と休憩を作成
                    $newAttendance = Attendance::create([
                        'user_id' => Auth::id(),
                        'date' => Carbon::now()->toDateString(),
                        'clock_in' => Carbon::now()->startOfDay(),
                    ]);

                    BreakRecord::create([
                        'attendance_id' => $newAttendance->id,
                        'break_start' => Carbon::now()->startOfDay(),
                        'break_end' => Carbon::now(),
                    ]);
                }
            }

        } else {
            $prevAttendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', now()->subDay()->toDateString())
                ->first();

            if ($prevAttendance && is_null($prevAttendance->clock_out)) {
                $prevAttendance->update(['clock_out' => Carbon::now()->endOfDay()]);
            }
            $newAttendance = Attendance::create([
                'user_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'clock_in' => Carbon::now()->startOfDay(),
            ]);

            $breakRecord = BreakRecord::where('attendance_id', $newAttendance->id)
                ->whereNull('break_end')
                ->first();

            if ($breakRecord) {
                $breakRecord->update(['break_end' => Carbon::now()]);
            }
        }

        return redirect()->back()->with('message', '休憩を終了しました。');
    }

    //日付一覧の表示
    public function viewAttendance(Request $request)
    {
        $today = Carbon::today();
        $date = $request->input('date', $today->toDateString());
        $users = User::all();

        $records = Attendance::with('user', 'breakRecords')->whereDate('date', $date)->paginate(5);

        foreach ($records as $record) {
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

            $actualWorkingTime = 0;
            if ($record->clock_in && $record->clock_out) {
                $clockIn = Carbon::parse($record->clock_in);
                $clockOut = Carbon::parse($record->clock_out);
                $actualWorkingTime = $clockIn->diffInSeconds($clockOut) - $totalBreakTime;
            }
            $record->actual_working = gmdate('H:i:s', $actualWorkingTime);
        }

        return view('attendance', compact('date', 'users', 'records'));
    }

    //日付横のボタンを押した時の動き
    public function viewByDate(Request $request)
    {
        $displayDate = Carbon::parse($request->input('displayDate'));

        if ($request->has('prevDate')) {
            $displayDate->subDay();
        }

        if ($request->has('nextDate')) {
            $displayDate->addDay();
        }

        return redirect()->route('attendance.view', ['date' => $displayDate->toDateString()]);
    }

    //ログインユーザの勤怠一覧表示
    public function viewUserAttendance(Request $request)
    {
        $records = Attendance::where('user_id', Auth::id())
        ->whereDate('date', now()->toDateString())
        ->paginate(5);

        foreach ($records as $record) {
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
            if ($record->clock_in && $record->clock_out) {
                $clockIn = Carbon::parse($record->clock_in);
                $clockOut = Carbon::parse($record->clock_out);
                $actualWorkingTime = $clockIn->diffInSeconds($clockOut) - $totalBreakTime;
            } else {
                $record->actual_working = '00:00:00';
            }
            $record->actual_working = gmdate('H:i:s', $actualWorkingTime);
        }

        return view('userattendance', compact('records'));
    }
}
