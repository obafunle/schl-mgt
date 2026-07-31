<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MAT', 'short_name' => 'Maths', 'category' => 'core', 'level' => 'junior'],
            ['name' => 'English Language', 'code' => 'ENG', 'short_name' => 'English', 'category' => 'core', 'level' => 'junior'],
            ['name' => 'Biology', 'code' => 'BIO', 'short_name' => 'Biology', 'category' => 'science', 'level' => 'senior'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'short_name' => 'Chem', 'category' => 'science', 'level' => 'senior'],
            ['name' => 'Physics', 'code' => 'PHY', 'short_name' => 'Physics', 'category' => 'science', 'level' => 'senior'],
            ['name' => 'History', 'code' => 'HIS', 'short_name' => 'History', 'category' => 'arts', 'level' => 'senior'],
            ['name' => 'Geography', 'code' => 'GEO', 'short_name' => 'Geo', 'category' => 'arts', 'level' => 'senior'],
            ['name' => 'Economics', 'code' => 'ECO', 'short_name' => 'Econ', 'category' => 'arts', 'level' => 'senior'],
            ['name' => 'Government', 'code' => 'GOV', 'short_name' => 'Govt', 'category' => 'arts', 'level' => 'senior'],
            ['name' => 'Literature in English', 'code' => 'LIT', 'short_name' => 'Literature', 'category' => 'arts', 'level' => 'senior'],
            ['name' => 'Christian Religious Knowledge', 'code' => 'CRK', 'short_name' => 'CRK', 'category' => 'core', 'level' => 'senior'],
            ['name' => 'Islamic Religious Knowledge', 'code' => 'IRK', 'short_name' => 'IRK', 'category' => 'core', 'level' => 'senior'],
            ['name' => 'Business Studies', 'code' => 'BUS', 'short_name' => 'Business', 'category' => 'vocational', 'level' => 'junior'],
            ['name' => 'Agricultural Science', 'code' => 'AGR', 'short_name' => 'Agric', 'category' => 'science', 'level' => 'junior'],
            ['name' => 'Computer Studies', 'code' => 'COM', 'short_name' => 'Computing', 'category' => 'science', 'level' => 'junior'],
            ['name' => 'French', 'code' => 'FRE', 'short_name' => 'French', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Yoruba', 'code' => 'YOR', 'short_name' => 'Yoruba', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Igbo', 'code' => 'IGB', 'short_name' => 'Igbo', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Hausa', 'code' => 'HAU', 'short_name' => 'Hausa', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Physical and Health Education', 'code' => 'PHE', 'short_name' => 'PHE', 'category' => 'core', 'level' => 'junior'],
            ['name' => 'Music', 'code' => 'MUS', 'short_name' => 'Music', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Fine Arts', 'code' => 'ART', 'short_name' => 'Arts', 'category' => 'arts', 'level' => 'junior'],
            ['name' => 'Technical Drawing', 'code' => 'TDR', 'short_name' => 'Tech Draw', 'category' => 'vocational', 'level' => 'senior'],
            ['name' => 'Further Mathematics', 'code' => 'FMAT', 'short_name' => 'Further Maths', 'category' => 'science', 'level' => 'senior'],
            ['name' => 'Civic Education', 'code' => 'CIV', 'short_name' => 'Civic', 'category' => 'core', 'level' => 'senior'],
            ['name' => 'Data Processing', 'code' => 'DAP', 'short_name' => 'Data Proc', 'category' => 'science', 'level' => 'senior'],
        ];

        foreach ($subjects as $data) {
            Subject::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'short_name' => $data['short_name'],
                'category' => $data['category'],
                'level' => $data['level'],
                'weekly_hours' => 4,
                'exam_weight' => 60,
                'ca_weight' => 40,
                'is_active' => true,
                'created_by' => 1,
            ]);
        }

        $this->command->info('✅ ' . count($subjects) . ' subjects created successfully!');
    }
}
