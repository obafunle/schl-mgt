<?php

namespace Database\Seeders;

use App\Models\TimetablePeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimetablePeriodsSeeder extends Seeder
{
    public function run()
    {
        // Get a default user ID (first admin or system user)
        $userId = DB::table('users')->first()?->id ?? 1;

        $periods = [
            ['name' => 'Period 1', 'code' => 'P1', 'start_time' => '08:00', 'end_time' => '08:45', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 1],
            ['name' => 'Period 2', 'code' => 'P2', 'start_time' => '08:50', 'end_time' => '09:35', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 2],
            ['name' => 'Period 3', 'code' => 'P3', 'start_time' => '09:40', 'end_time' => '10:25', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 3],
            ['name' => 'Break', 'code' => 'BRK', 'start_time' => '10:25', 'end_time' => '10:45', 'duration_minutes' => 20, 'type' => 'break', 'order' => 4],
            ['name' => 'Period 4', 'code' => 'P4', 'start_time' => '10:45', 'end_time' => '11:30', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 5],
            ['name' => 'Period 5', 'code' => 'P5', 'start_time' => '11:35', 'end_time' => '12:20', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 6],
            ['name' => 'Period 6', 'code' => 'P6', 'start_time' => '12:25', 'end_time' => '13:10', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 7],
            ['name' => 'Lunch', 'code' => 'LCH', 'start_time' => '13:10', 'end_time' => '14:00', 'duration_minutes' => 50, 'type' => 'break', 'order' => 8],
            ['name' => 'Period 7', 'code' => 'P7', 'start_time' => '14:00', 'end_time' => '14:45', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 9],
            ['name' => 'Period 8', 'code' => 'P8', 'start_time' => '14:50', 'end_time' => '15:35', 'duration_minutes' => 45, 'type' => 'academic', 'order' => 10],
        ];

        foreach ($periods as $period) {
            TimetablePeriod::create(array_merge($period, [
                'created_by' => $userId,
                'is_active' => true,
            ]));
        }
    }
}