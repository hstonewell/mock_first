<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakRecord;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {

        // データを取得
        $attendances = Attendance::all();
        $breakRecords = BreakRecord::all();

        // データが正しくシードされているかを確認
        $this->assertEmpty($attendances);
        $this->assertEmpty($breakRecords);

        // 休憩データの整合性を確認
        $breakRecords->each(function ($break) {
            $attendance = Attendance::find($break->attendance_id);

            $this->assertNotNull($attendance);
            $this->assertTrue($break->break_start->betweenDateTime($attendance->clock_in, $attendance->clock_out));
            $this->assertTrue($break->break_end->betweenDateTime($attendance->clock_in, $attendance->clock_out));
        });
    }
}
