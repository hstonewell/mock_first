<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Attendance::class;

    public function definition()
    {
        $startDate = Carbon::create(2024, 7, 1);
        $endDate = Carbon::create(2024, 7, 31);

        $randomDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

        $clockIn = $randomDate->copy()->setTime(rand(7, 10), rand(0, 59));
        $clockOut = $clockIn->copy()->addHours(rand(8, 10))->addMinutes(rand(0, 59));

        return [
            'user_id' => rand(1,15),
            'date' => $randomDate->toDateString(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut
        ];
    }
}

