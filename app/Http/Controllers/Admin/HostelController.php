<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelBedAssignment;
use App\Models\HostelAttendance;
use App\Models\HostelComplaint;
use App\Models\HostelFee;
use App\Models\Student;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HostelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_hostel');
    }

    /**
     * Display a listing of hostels
     */
    public function index(Request $request)
    {
        $query = Hostel::with(['rooms', 'createdBy']);

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        $hostels = $query->latest()->paginate(10);
        $types = ['boys', 'girls', 'mixed'];
        $genders = ['male', 'female', 'mixed'];

        return view('admin.hostels.index', compact('hostels', 'types', 'genders'));
    }

    /**
     * Show the form for creating a new hostel
     */
    public function create()
    {
        return view('admin.hostels.create');
    }

    /**
     * Store a newly created hostel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hostels',
            'gender' => 'required|in:male,female,mixed',
            'type' => 'required|in:boys,girls,mixed',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'house_master' => 'nullable|string|max:255',
            'assistant_house_master' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $hostel = Hostel::create($validated);

        activity()
            ->performedOn($hostel)
            ->causedBy(auth()->user())
            ->log('Created hostel: ' . $hostel->name);

        return redirect()->route('admin.hostels.index')
            ->with('success', 'Hostel "' . $hostel->name . '" created successfully!');
    }

    /**
     * Display the specified hostel
     */
    public function show(Hostel $hostel)
    {
        $hostel->load(['rooms', 'assignments.student', 'complaints' => function ($q) {
            $q->whereIn('status', ['pending', 'in_progress'])->orderBy('created_at', 'desc');
        }]);

        // Get statistics
        $stats = [
            'total_rooms' => $hostel->rooms()->count(),
            'total_beds' => $hostel->rooms()->sum('capacity'),
            'occupied_beds' => $hostel->assignments()->where('status', 'active')->count(),
            'available_beds' => $hostel->rooms()->sum('capacity') - $hostel->assignments()->where('status', 'active')->count(),
            'pending_complaints' => $hostel->complaints()->where('status', 'pending')->count(),
            'today_attendance' => $hostel->attendance()->whereDate('date', today())->count(),
        ];

        $stats['occupancy_rate'] = $stats['total_beds'] > 0
            ? round(($stats['occupied_beds'] / $stats['total_beds']) * 100, 2)
            : 0;

        return view('admin.hostels.show', compact('hostel', 'stats'));
    }

    /**
     * Show the form for editing the specified hostel
     */
    public function edit(Hostel $hostel)
    {
        return view('admin.hostels.edit', compact('hostel'));
    }

    /**
     * Update the specified hostel
     */
    public function update(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hostels,code,' . $hostel->id,
            'gender' => 'required|in:male,female,mixed',
            'type' => 'required|in:boys,girls,mixed',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'house_master' => 'nullable|string|max:255',
            'assistant_house_master' => 'nullable|string|max:255',
            'facilities' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $hostel->update($validated);

        activity()
            ->performedOn($hostel)
            ->causedBy(auth()->user())
            ->log('Updated hostel: ' . $hostel->name);

        return redirect()->route('admin.hostels.index')
            ->with('success', 'Hostel "' . $hostel->name . '" updated successfully!');
    }

    /**
     * Remove the specified hostel
     */
    public function destroy(Hostel $hostel)
    {
        if ($hostel->assignments()->where('status', 'active')->count() > 0) {
            return back()->with('error', 'Cannot delete hostel with active bed assignments.');
        }

        $name = $hostel->name;
        $hostel->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted hostel: ' . $name);

        return redirect()->route('admin.hostels.index')
            ->with('success', 'Hostel "' . $name . '" deleted successfully!');
    }

    // ==========================================================
    // ROOM MANAGEMENT
    // ==========================================================

    /**
     * Manage rooms for a hostel
     */
    public function rooms(Hostel $hostel)
    {
        $rooms = $hostel->rooms()->latest()->paginate(20);
        return view('admin.hostels.rooms', compact('hostel', 'rooms'));
    }

    /**
     * Store a new room in a hostel
     */
    public function storeRoom(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'floor' => 'nullable|string|max:50',
            'block' => 'nullable|string|max:50',
            'room_type' => 'required|in:dormitory,shared,single,suite',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        // Check if room number already exists in this hostel
        $existing = HostelRoom::where('hostel_id', $hostel->id)
            ->where('room_number', $validated['room_number'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Room number "' . $validated['room_number'] . '" already exists in this hostel.');
        }

        $validated['hostel_id'] = $hostel->id;
        $validated['occupied'] = 0;
        $validated['available'] = $validated['capacity'];
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $room = HostelRoom::create($validated);

        // Update hostel counts
        $hostel->updateCounts();

        activity()
            ->performedOn($room)
            ->causedBy(auth()->user())
            ->log('Added room to hostel: ' . $hostel->name . ' - Room ' . $room->room_number);

        return back()->with('success', 'Room "' . $room->room_number . '" added successfully!');
    }

    // ==========================================================
    // STUDENT ASSIGNMENT
    // ==========================================================

    /**
     * Assign student to a room
     */
    public function assignStudent(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'bed_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if room has available beds
        $room = HostelRoom::findOrFail($validated['hostel_room_id']);
        if ($room->isFull()) {
            return back()->with('error', 'This room is full. Cannot assign more students.');
        }

        // Check if student already has an active assignment
        $existing = HostelBedAssignment::where('student_id', $validated['student_id'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->with('error', 'This student already has an active hostel assignment.');
        }

        $term = Term::where('is_current', true)->first();
        $academicYear = AcademicYear::where('is_current', true)->first();

        if (!$term || !$academicYear) {
            return back()->with('error', 'No active term or academic year found.');
        }

        $assignment = HostelBedAssignment::create([
            'student_id' => $validated['student_id'],
            'hostel_id' => $hostel->id,
            'hostel_room_id' => $validated['hostel_room_id'],
            'bed_number' => $validated['bed_number'] ?? null,
            'assigned_date' => now(),
            'status' => 'active',
            'term_id' => $term->id,
            'academic_year_id' => $academicYear->id,
            'notes' => $validated['notes'] ?? null,
            'assigned_by' => auth()->id(),
        ]);

        // Update room counts
        $room->updateCounts();

        activity()
            ->performedOn($assignment)
            ->causedBy(auth()->user())
            ->log('Assigned student to hostel: ' . $hostel->name);

        return back()->with('success', 'Student assigned successfully!');
    }

    /**
     * Release a student from hostel
     */
    public function releaseStudent(HostelBedAssignment $assignment)
    {
        $assignment->release();

        activity()
            ->performedOn($assignment)
            ->causedBy(auth()->user())
            ->log('Released student from hostel assignment');

        return back()->with('success', 'Student released from hostel successfully!');
    }

    // ==========================================================
    // ATTENDANCE MANAGEMENT
    // ==========================================================

    /**
     * Show hostel attendance
     */
    public function attendance(Hostel $hostel, Request $request)
    {
        $date = $request->get('date', today());
        $attendance = HostelAttendance::where('hostel_id', $hostel->id)
            ->whereDate('date', $date)
            ->with(['student', 'room'])
            ->get();

        $students = $hostel->assignments()
            ->where('status', 'active')
            ->with(['student'])
            ->get();

        return view('admin.hostels.attendance', compact('hostel', 'attendance', 'students', 'date'));
    }

    /**
     * Mark hostel attendance
     */
    public function markAttendance(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:present,absent,late,excused,weekend',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get student's room assignment
        $assignment = HostelBedAssignment::where('student_id', $validated['student_id'])
            ->where('status', 'active')
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Student has no active room assignment.');
        }

        $attendance = HostelAttendance::create([
            'student_id' => $validated['student_id'],
            'hostel_id' => $hostel->id,
            'hostel_room_id' => $assignment->hostel_room_id,
            'date' => today(),
            'status' => $validated['status'],
            'check_in' => $validated['check_in'] ?? null,
            'check_out' => $validated['check_out'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'marked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Attendance marked for student successfully!');
    }

    // ==========================================================
    // COMPLAINT MANAGEMENT
    // ==========================================================

    /**
     * Show hostel complaints
     */
    public function complaints(Hostel $hostel)
    {
        $complaints = $hostel->complaints()
            ->with(['student', 'assignedTo'])
            ->latest()
            ->paginate(20);

        return view('admin.hostels.complaints', compact('hostel', 'complaints'));
    }

    // ==========================================================
    // HOSTEL FEES
    // ==========================================================

    /**
     * Show hostel fees
     */
    public function fees(Hostel $hostel)
    {
        $fees = $hostel->fees()->latest()->paginate(20);
        return view('admin.hostels.fees', compact('hostel', 'fees'));
    }

    // ==========================================================
    // AJAX ENDPOINTS
    // ==========================================================

    /**
     * Get students for assignment (AJAX)
     */
    public function getStudents(Request $request)
    {
        $students = Student::where('status', 'active')
            ->whereDoesntHave('hostelAssignments', function ($q) {
                $q->where('status', 'active');
            })
            ->when($request->has('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhere('admission_number', 'LIKE', "%{$search}%");
                });
            })
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        return response()->json($students);
    }
}
