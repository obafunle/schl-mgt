<?php

namespace Database\Seeders;

use App\Models\TimetableDay;
use Illuminate\Database\Seeder;

class TimetableDaysSeeder extends Seeder
{
    public function run()
    {
        $days = [
            ['name' => 'Monday', 'short_name' => 'Mon', 'day_number' => 1, 'is_school_day' => true, 'order' => 1],
            ['name' => 'Tuesday', 'short_name' => 'Tue', 'day_number' => 2, 'is_school_day' => true, 'order' => 2],
            ['name' => 'Wednesday', 'short_name' => 'Wed', 'day_number' => 3, 'is_school_day' => true, 'order' => 3],
            ['name' => 'Thursday', 'short_name' => 'Thu', 'day_number' => 4, 'is_school_day' => true, 'order' => 4],
            ['name' => 'Friday', 'short_name' => 'Fri', 'day_number' => 5, 'is_school_day' => true, 'order' => 5],
            ['name' => 'Saturday', 'short_name' => 'Sat', 'day_number' => 6, 'is_school_day' => false, 'order' => 6],
            ['name' => 'Sunday', 'short_name' => 'Sun', 'day_number' => 7, 'is_school_day' => false, 'order' => 7],
        ];

        foreach ($days as $day) {
            TimetableDay::create($day);
        }
    }
}