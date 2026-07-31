<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
    }

    /** @test */
    public function admin_can_view_students_list()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/students');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.students.index');
    }

    /** @test */
    public function admin_can_create_student()
    {
        $class = ClassModel::factory()->create();
        $academicYear = AcademicYear::factory()->create();

        $studentData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'admission_number' => '2024-0001',
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'parent_name' => 'Mr. Doe',
            'parent_phone' => '08012345678',
            'date_of_birth' => '2010-01-01',
            'gender' => 'male'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/students', $studentData);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);
    }

    /** @test */
    public function admin_can_edit_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/students/{$student->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.students.edit');
        $response->assertViewHas('student', $student);
    }

    /** @test */
    public function admin_can_update_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/admin/students/{$student->id}", [
                'first_name' => 'Updated Name',
                'last_name' => $student->last_name,
                'email' => $student->email,
                'admission_number' => $student->admission_number,
                'class_id' => $student->class_id,
                'academic_year_id' => $student->academic_year_id,
                'parent_name' => $student->parent_name,
                'parent_phone' => $student->parent_phone,
                'date_of_birth' => $student->date_of_birth,
                'gender' => $student->gender
            ]);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Updated Name'
        ]);
    }

    /** @test */
    public function admin_can_delete_student()
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/students/{$student->id}");

        $response->assertRedirect('/admin/students');
        $this->assertSoftDeleted('students', [
            'id' => $student->id
        ]);
    }

    /** @test */
    public function student_has_class_relationship()
    {
        $class = ClassModel::factory()->create();
        $student = Student::factory()->create([
            'class_id' => $class->id
        ]);

        $this->assertEquals($class->id, $student->class->id);
        $this->assertEquals($student->id, $class->students->first()->id);
    }
}