<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HostelManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
    }

    /** @test */
    public function admin_can_create_hostel()
    {
        $hostelData = [
            'name' => 'Boys Hostel',
            'code' => 'BH01',
            'type' => 'boys',
            'gender' => 'male',
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/hostels', $hostelData);

        $response->assertRedirect('/admin/hostels');
        $this->assertDatabaseHas('hostels', [
            'name' => 'Boys Hostel',
            'code' => 'BH01'
        ]);
    }

    /** @test */
    public function admin_can_add_room_to_hostel()
    {
        $hostel = Hostel::factory()->create();

        $roomData = [
            'room_number' => '101',
            'capacity' => 4,
            'room_type' => 'shared'
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/hostels/' . $hostel->id . '/rooms', $roomData);

        $response->assertRedirect();
        $this->assertDatabaseHas('hostel_rooms', [
            'hostel_id' => $hostel->id,
            'room_number' => '101'
        });
    }

    /** @test */
    public function student_can_be_assigned_to_room()
    {
        $hostel = Hostel::factory()->create();
        $room = HostelRoom::factory()->create([
            'hostel_id' => $hostel->id,
            'capacity' => 4,
            'occupied' => 0
        ]);
        $student = Student::factory()->create();

        $assignmentData = [
            'student_id' => $student->id,
            'hostel_room_id' => $room->id
        ];

        $response = $this->actingAs($this->admin)
            ->post("/admin/hostels/{$hostel->id}/assign", $assignmentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('hostel_bed_assignments', [
            'student_id' => $student->id,
            'hostel_id' => $hostel->id,
            'hostel_room_id' => $room->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function hostel_room_updates_occupancy_on_assignment()
    {
        $room = HostelRoom::factory()->create([
            'capacity' => 4,
            'occupied' => 0,
            'available' => 4
        ]);

        // Create assignment
        $student = Student::factory()->create();
        $room->assignments()->create([
            'student_id' => $student->id,
            'hostel_id' => $room->hostel_id,
            'assigned_date' => now(),
            'status' => 'active',
            'term_id' => 1,
            'academic_year_id' => 1,
            'assigned_by' => 1
        ]);

        $room->updateCounts();

        $this->assertEquals(1, $room->fresh()->occupied);
        $this->assertEquals(3, $room->fresh()->available);
    }
}