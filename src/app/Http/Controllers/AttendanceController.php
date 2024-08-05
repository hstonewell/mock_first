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
            $today = Carbon::today();
            $activeAttendance = Attendance::where('user_id', Auth::id())->activeRecord()->first();

            $activeBreak = BreakRecord::whereHas('attendance', function ($query) {
                $query->where('user_id', Auth::id());
            })->activeBreak()->first();

            $clockInDisabled = $activeAttendance ? true : false;
            $clockOutDisabled = $activeAttendance && is_null($activeAttendance->clock_out) ? false : true;
            $breakStartDisabled = $activeAttendance && is_null($activeAttendance->clock_out) && is_null($activeBreak) ? false : true;
            $breakEndDisabled = $activeBreak ? false : true;

            return view('index', compact('activeAttendance', 'activeBreak', 'clockInDisabled', 'clockOutDisabled', 'breakStartDisabled', 'breakEndDisabled'));
        }
        return redirect('login');
    }

    //勤務開始
    public function clockIn(Request $request)
    {
        $this->handleMidnightShift();

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '出勤打刻が完了しました。');
    }

    //勤務終了
    public function clockOut(Request $request)
    {
        $this->handleMidnightShift();

        $activeAttendance = Attendance::where('user_id', auth()->id())
        ->whereNull('clock_out')
        ->first();

        if ($activeAttendance) {
            $activeAttendance->update(['clock_out' => Carbon::now()]);
        }

        return redirect()->back()->with('message', '退勤打刻が完了しました。本日もお疲れ様でした。');
    }

    //休憩開始
    public function breakStart(Request $request)
    {
        $this->handleMidnightShift();

        $activeAttendance = Attendance::where('user_id', auth()->id())
        ->whereNull('clock_out')
        ->first();

        BreakRecord::create([
            'attendance_id' => $activeAttendance->id,
            'break_start' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', '休憩を開始しました。');
    }

    //休憩終了
    public function breakEnd(Request $request)
    {
        $this->handleMidnightShift();

        $activeAttendance = Attendance::where('user_id', auth()->id())
        ->whereNull('clock_out')
        ->first();

        $breakRecord = BreakRecord::where('attendance_id', $activeAttendance->id)
        ->whereNull('break_end')
        ->first();

        if ($breakRecord) {
            $breakRecord->update(['break_end' => Carbon::now()]);
        }

        return redirect()->back()->with('message', '休憩を終了しました。');
    }

    //日を跨いだ時の処理
    private function handleMidnightShift()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
        ->whereNull('clock_out')
        ->first();

        if ($attendance) {
            $clockIn = Carbon::parse($attendance->clock_in);
            if ($clockIn->toDateString() !== $today) {
                $attendance->update(['clock_out' => $clockIn->copy()->endOfDay()->subSecond()]);

            Attendance::create([
                'user_id' => Auth::id(),
                'date' => $today,
                'clock_in' => $now->startOfDay(),
            ]);
            }
        }

        $breakRecords = BreakRecord::whereHas('attendance', function ($query) {
            $query->where('user_id', Auth::id())
                ->whereNull('clock_out');
        })->whereNull('break_end')->get();

        foreach ($breakRecords as $breakRecord) {
            $breakStart = Carbon::parse($breakRecord->break_start);

            if ($breakStart->toDateString() !== $today) {
            $breakRecord->update(['break_end' => Carbon::create($yesterday)->endOfDay()->subSecond()]);
            BreakRecord::create([
                'attendance_id' => $attendance->id,
                'break_start' => $now->startOfDay(),
            ]);
            }
        }
    }

    //日付一覧の表示
    public function viewAttendance(Request $request)
    {
        $today = Carbon::today();
        $date = $request->input('date', $today->toDateString());
        $users = User::all();

        $records = Attendance::finishedRecord()->with('user', 'breakRecords')->whereDate('date', $date)->paginate(5);

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
        $user = Auth::check();

        $records = Attendance::where('user_id', Auth::id())
        ->finishedRecord()
        ->orderBy('date', 'desc')
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

        return view('user_attendance', compact('user', 'records'));
    }

    //会員登録後のビュー表示
    public function viewRegistered()
    {
        return view('registered');
    }
}
