<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        if(Auth::check()) {
            return view('index')
        }
        //$records = AttendanceRecord::where('user_id', Auth::id())->with('breakRecords')->get();
        //return view('index', compact('records'));
    }

    public function clockIn()
    {
        $oldRecord = AttendanceRecord::where('user_id', Auth::id())->latest()->first();

        if($oldRecord){
            $oldRecordClockIn = new Carbon($oldRecord->clockIn);
            $oldRecordDay = $oldRecordClockIn->startOfDay();
        } else if($oldRecord){
            $oldTimeClockOut = new Carbon($oldrecord->clockOut);
            $oldDay = $oldTimeClockOut->startOfDay();

        } else {
            //0時を超えたら新しいレコード
            $Record = AttendanceRecord::create([
                'user_id' => Auth::id(),
                'date' => Carbon::now()->toDateString(),
                'clock_in' => Carbon::now()->toTimeString(),
            ]);
            return redirect()->back();
            //メッセージ追加。打刻完了
        };

        $newRecordDay = Carbon::today();

        if(($oldRecordDay == $newRecordDay) && (empty($oldRecord->clockOut))){
            return redirect()->back();
        //エラーメッセージを追加。すでに打刻済み。
        }

        $record = AttendanceRecord::create([
            'user_id' => Auth::id(),
            'date' => Carbon::now()->toDateString(),
            'clock_in' => Carbon::now()->toTimeString(),
        ]);
        return redirect()->back();
            //メッセージ追加。打刻完了
    }

    public function clockOut()
    {
        $record = AttendanceRecord::where('user_id', Auth::id())->latest()->first();

        $record->update([
            'clock_out' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back();
        //メッセージ追加。打刻完了
    }
}
