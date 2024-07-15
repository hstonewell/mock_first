<?php

namespace Database\Factories;

use App\Models\BreakRecord;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BreakRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = BreakRecord::class;

    public function definition()
    {
        // 出退勤データを取得する
        $attendance = Attendance::inRandomOrder()->first();

        // 出勤開始と終了の変数を定義
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);

        // 出勤時間内のランダムな休憩開始時間を生成
        $breakStart = $clockIn->copy()->addMinutes(rand(0, $clockOut->diffInMinutes($clockIn) - 15));
        $breakEnd = $breakStart->copy()->addMinutes(rand(15, 60));

        // 休憩終了時間が退勤時間を超えないようにする
        if ($breakEnd->greaterThan($clockOut)) {
            $breakEnd = $clockOut->copy();
        }

        return [
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];
    }
}