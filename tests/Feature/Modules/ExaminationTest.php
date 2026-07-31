<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Examination;
use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExaminationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
        
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');
    }

    /** @test */
    public function admin_can_create_examination()
    {
        $class = ClassModel::factory()->create();
        $subject = Subject::factory()->create();
        $term = Term::factory()->create();
        $academicYear = AcademicYear::factory()->create();

        $examData = [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'academic_year_id' => $academicYear->id,
            'exam_date' => now()->format('Y-m-d'),
            'ca_weight' => 40,
            'exam_weight' => 60,
            'total_marks' => 100
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/examinations', $examData);

        $response->assertRedirect('/admin/examinations');
        $this->assertDatabaseHas('examinations', [
            'class_id' => $class->id,
            'subject_id' => $subject->id
        ]);
    }

    /** @test */
    public function teacher_can_enter_grades()
    {
        $student = Student::factory()->create();
        $examination = Examination::factory()->create();

        $gradeData = [
            'grades' => [
                [
                    'student_id' => $student->id,
                    'ca_score' => 35,
                    'exam_score' => 45
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)
            ->post("/admin/examinations/{$examination->id}/store-grades", $gradeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'ca_score' => 35,
            'exam_score' => 45
        ]);
    }

    /** @test */
    public function grade_calculates_total_correctly()
    {
        $grade = Grade::factory()->create([
            'ca_score' => 35,
            'exam_score' => 45
        ]);

        $grade->calculateTotal();
        
        $this->assertEquals(80, $grade->total_score);
    }

    /** @test */
    public function grade_determines_correct_nigerian_grade()
    {
        $grade = Grade::factory()->create(['total_score' => 75]);
        $this->assertEquals('A', $grade->calculateGrade());

        $grade = Grade::factory()->create(['total_score' => 55]);
        $this->assertEquals('C', $grade->calculateGrade());

        $grade = Grade::factory()->create(['total_score' => 25]);
        $this->assertEquals('F', $grade->calculateGrade());
    }
}