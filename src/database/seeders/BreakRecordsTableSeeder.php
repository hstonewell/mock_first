<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BreakRecord;
use App\Models\Attendance;

class BreakRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendances = Attendance::factory()->count(500)->create();

        $attendances->each(function ($attendance) {
            BreakRecord::factory()->create([
                'attendance_id' => $attendance->id,
            ]);
        });
    }
}
