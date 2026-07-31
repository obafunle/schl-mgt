<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSubject;
use App\Models\StaffLeaveRequest;
use App\Models\StaffAttendance;
use App\Models\StaffPayroll;
use App\Models\StaffPerformanceReview;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_staff')->only(['index', 'show']);
        $this->middleware('permission:create_staff')->only(['create', 'store']);
        $this->middleware('permission:edit_staff')->only(['edit', 'update']);
        $this->middleware('permission:delete_staff')->only(['destroy']);
        $this->middleware('permission:manage_staff')->only([
            'toggleStatus', 'bulkAction', 'approveLeaveRequest', 'rejectLeaveRequest',
            'processPayroll', 'markPayrollPaid', 'approvePerformanceReview'
        ]);
    }

    /**
     * Display a listing of staff
     */
    public function index(Request $request)
    {
        $query = Staff::with(['user', 'classAssigned']);

        // Filters
        if ($request->has('staff_type') && $request->staff_type) {
            $query->where('staff_type', $request->staff_type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('staff_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $staff = $query->latest()->paginate(20);
        $staffTypes = ['teacher', 'admin', 'support', 'accountant', 'librarian', 'other'];
        $statuses = ['active', 'inactive', 'suspended', 'terminated'];
        $employmentTypes = ['permanent', 'contract', 'part-time', 'intern'];

        return view('admin.staff.index', compact('staff', 'staffTypes', 'statuses', 'employmentTypes'));
    }

    /**
     * Show the form for creating a new staff
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();

        return view('admin.staff.create', compact('subjects', 'classes', 'classArms'));
    }

    /**
     * Store a newly created staff
     * ✅ FIXED: Changed emergency_contact_* to next_of_kin_*
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|unique:staff,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'staff_type' => 'required|in:teacher,admin,support,accountant,librarian,other',
            'employment_type' => 'required|in:permanent,contract,part-time,intern',
            'hire_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_account_name' => 'nullable|string|max:255',
            'next_of_kin_name' => 'nullable|string|max:255',
            'next_of_kin_phone' => 'nullable|string|max:20',
            'next_of_kin_relationship' => 'nullable|string|max:255',
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => 'required_with:qualifications|string',
            'qualifications.*.institution' => 'required_with:qualifications|string',
            'qualifications.*.year' => 'required_with:qualifications|string',
            'experience' => 'nullable|array',
            'experience.*.position' => 'required_with:experience|string',
            'experience.*.school' => 'required_with:experience|string',
            'experience.*.years' => 'required_with:experience|numeric|min:0',
            'create_user_account' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_user_account,true|min:8|confirmed',
            'subjects' => 'nullable|array',
            'subjects.*.subject_id' => 'required_with:subjects|exists:subjects,id',
            'subjects.*.class_id' => 'nullable|exists:classes,id',
            'subjects.*.role' => 'nullable|in:primary,secondary,assistant',
            'subjects.*.weekly_hours' => 'nullable|integer|min:1|max:40',
        ]);

        // Generate staff ID
        $validated['staff_id'] = $this->generateStaffId();
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'active';

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        DB::transaction(function () use ($validated, $request) {
            // Create staff
            $staff = Staff::create($validated);

            // Create user account if requested
            if ($request->has('create_user_account') && $request->create_user_account) {
                $user = User::create([
                    'name' => $staff->full_name,
                    'email' => $staff->email,
                    'password' => Hash::make($request->password),
                ]);
                // Assign role based on staff type
                $role = $this->getRoleForStaffType($staff->staff_type);
                $user->assignRole($role);
            }

            // Assign subjects (if teacher)
            if ($staff->staff_type === 'teacher' && $request->has('subjects')) {
                foreach ($request->subjects as $subjectData) {
                    StaffSubject::create([
                        'staff_id' => $staff->id,
                        'subject_id' => $subjectData['subject_id'],
                        'class_id' => $subjectData['class_id'] ?? null,
                        'role' => $subjectData['role'] ?? 'primary',
                        'weekly_hours' => $subjectData['weekly_hours'] ?? 4,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            activity()
                ->performedOn($staff)
                ->causedBy(auth()->user())
                ->log('Created staff: ' . $staff->full_name . ' (ID: ' . $staff->staff_id . ')');
        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff created successfully! Staff ID: ' . $validated['staff_id']);
    }

    /**
     * Display the specified staff
     */
    public function show(Staff $staff)
    {
        $staff->load([
            'subjects',
            'classSubjects.class',
            'leaveRequests' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'attendance' => function ($query) {
                $query->orderBy('date', 'desc')->limit(30);
            },
            'payrolls' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(6);
            },
            'performanceReviews' => function ($query) {
                $query->orderBy('review_date', 'desc')->limit(5);
            },
            'classAssigned'
        ]);

        // Calculate statistics
        $stats = [
            'total_days_present' => $staff->attendance()->where('status', 'present')->count(),
            'total_days_absent' => $staff->attendance()->where('status', 'absent')->count(),
            'total_leave_days' => $staff->leaveRequests()->approved()->sum('total_days'),
            'pending_leave_requests' => $staff->leaveRequests()->pending()->count(),
            'average_rating' => $staff->performanceReviews()->approved()->avg('overall_rating'),
            'total_subjects' => $staff->subjects()->count(),
            'total_attendances' => $staff->attendance()->count(),
            'total_payrolls' => $staff->payrolls()->count(),
        ];

        return view('admin.staff.show', compact('staff', 'stats'));
    }

    /**
     * Show the form for editing the specified staff
     */
    public function edit(Staff $staff)
    {
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();
        $assignedSubjects = $staff->classSubjects()->get();

        return view('admin.staff.edit', compact('staff', 'subjects', 'classes', 'classArms', 'assignedSubjects'));
    }

    /**
     * Update the specified staff
     * ✅ FIXED: Changed emergency_contact_* to next_of_kin_*
     * ✅ FIXED: Added password update
     */
    public function update(Request $request, Staff $staff)
    {
            // 🔍 DEBUG: Log all incoming request data
    \Log::info('=== UPDATE REQUEST DATA ===', $request->all());
    \Log::info('Next of Kin Name:', ['value' => $request->input('next_of_kin_name')]);
    \Log::info('Next of Kin Phone:', ['value' => $request->input('next_of_kin_phone')]);
    \Log::info('Next of Kin Relationship:', ['value' => $request->input('next_of_kin_relationship')]);
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|unique:staff,email,' . $staff->id . '|unique:users,email,' . ($staff->user?->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'staff_type' => 'required|in:teacher,admin,support,accountant,librarian,other',
            'employment_type' => 'required|in:permanent,contract,part-time,intern',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'status' => 'required|in:active,inactive,suspended,terminated',
            'basic_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:20',
            'bank_account_name' => 'nullable|string|max:255',
            'next_of_kin_name' => 'nullable|string|max:255',
            'next_of_kin_phone' => 'nullable|string|max:20',
            'next_of_kin_relationship' => 'nullable|string|max:255',
            'qualifications' => 'nullable|array',
            'qualifications.*.degree' => 'required_with:qualifications|string',
            'qualifications.*.institution' => 'required_with:qualifications|string',
            'qualifications.*.year' => 'required_with:qualifications|string',
            'experience' => 'nullable|array',
            'experience.*.position' => 'required_with:experience|string',
            'experience.*.school' => 'required_with:experience|string',
            'experience.*.years' => 'required_with:experience|numeric|min:0',
            'subjects' => 'nullable|array',
            'subjects.*.subject_id' => 'required_with:subjects|exists:subjects,id',
            'subjects.*.class_id' => 'nullable|exists:classes,id',
            'subjects.*.role' => 'nullable|in:primary,secondary,assistant',
            'subjects.*.weekly_hours' => 'nullable|integer|min:1|max:40',
            // ✅ ADDED: Password validation
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        DB::transaction(function () use ($staff, $validated, $request) {
            // ✅ Update staff with ALL fields including next_of_kin
            $staff->update($validated);

            // Update user account role if staff type changed
            if ($staff->user) {
                $role = $this->getRoleForStaffType($staff->staff_type);
                $staff->user->syncRoles([$role]);
            }

            // ✅ If password is provided, update user password
            if ($request->filled('password') && $staff->user) {
                $staff->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Update subject assignments (if teacher)
            if ($staff->staff_type === 'teacher' && $request->has('subjects')) {
                // Remove old assignments
                $staff->classSubjects()->delete();

                // Add new assignments
                foreach ($request->subjects as $subjectData) {
                    StaffSubject::create([
                        'staff_id' => $staff->id,
                        'subject_id' => $subjectData['subject_id'],
                        'class_id' => $subjectData['class_id'] ?? null,
                        'role' => $subjectData['role'] ?? 'primary',
                        'weekly_hours' => $subjectData['weekly_hours'] ?? 4,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            activity()
                ->performedOn($staff)
                ->causedBy(auth()->user())
                ->log('Updated staff: ' . $staff->full_name);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff updated successfully!');
    }

    /**
     * Remove the specified staff
     */
    public function destroy(Staff $staff)
    {
        // Check if staff has any dependencies
        if ($staff->classAssigned) {
            return back()->with('error', 'Cannot delete staff. They are assigned as a class teacher.');
        }

        if ($staff->subjects()->count() > 0) {
            return back()->with('error', 'Cannot delete staff. They are assigned to subjects.');
        }

        if ($staff->user) {
            $staff->user->delete();
        }

        if ($staff->photo) {
            Storage::disk('public')->delete($staff->photo);
        }

        $staffName = $staff->full_name;
        $staffId = $staff->staff_id;
        $staff->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted staff: ' . $staffName . ' (ID: ' . $staffId . ')');

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff deleted successfully!');
    }

    /**
     * Toggle staff status
     */
    public function toggleStatus(Staff $staff)
    {
        $statuses = ['active', 'inactive', 'suspended', 'terminated'];
        $currentIndex = array_search($staff->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);

        $oldStatus = $staff->status;
        $staff->status = $statuses[$nextIndex];
        $staff->save();

        // If status is terminated, also update user account
        if ($staff->status === 'terminated' && $staff->user) {
            $staff->user->update(['is_active' => false]);
        }

        activity()
            ->performedOn($staff)
            ->causedBy(auth()->user())
            ->log('Changed staff status from ' . $oldStatus . ' to ' . $staff->status . ' for ' . $staff->full_name);

        return back()->with('success', 'Staff status updated to: ' . ucfirst($staff->status));
    }

    /**
     * Generate staff ID
     */
    private function generateStaffId()
    {
        $prefix = 'STF-' . date('Y') . '-';
        $lastStaff = Staff::where('staff_id', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStaff) {
            $lastNumber = intval(substr($lastStaff->staff_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Upload photo
     */
    private function uploadPhoto($photo)
    {
        $filename = time() . '-' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('staff/photos', $filename, 'public');

        try {
            $image = Image::make(Storage::disk('public')->path($path));
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->save();
        } catch (\Exception $e) {
            Log::warning('Could not resize image: ' . $e->getMessage());
        }

        return $path;
    }

    /**
     * Get role for staff type
     */
    private function getRoleForStaffType($staffType)
    {
        $roles = [
            'teacher' => 'teacher',
            'admin' => 'admin',
            'accountant' => 'accountant',
            'librarian' => 'admin',
            'support' => 'admin',
            'other' => 'admin'
        ];

        return $roles[$staffType] ?? 'admin';
    }

    // ==========================================================
    // LEAVE MANAGEMENT
    // ==========================================================

    public function leaveRequests(Staff $staff)
    {
        $leaveRequests = $staff->leaveRequests()->latest()->paginate(20);
        return view('admin.staff.leave-requests', compact('staff', 'leaveRequests'));
    }

    public function storeLeaveRequest(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,maternity,paternity,study,compassionate,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $start->diffInDays($end) + 1;
        $validated['staff_id'] = $staff->id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        $leaveRequest = StaffLeaveRequest::create($validated);

        activity()
            ->performedOn($leaveRequest)
            ->causedBy(auth()->user())
            ->log('Staff requested leave: ' . $staff->full_name . ' (' . $validated['leave_type'] . ')');

        return back()->with('success', 'Leave request submitted successfully!');
    }

    public function approveLeaveRequest(Staff $staff, StaffLeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'This leave request is already ' . $leaveRequest->status);
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        activity()
            ->performedOn($leaveRequest)
            ->causedBy(auth()->user())
            ->log('Approved leave request for: ' . $staff->full_name);

        return back()->with('success', 'Leave request approved!');
    }

    public function rejectLeaveRequest(Request $request, Staff $staff, StaffLeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'This leave request is already ' . $leaveRequest->status);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        activity()
            ->performedOn($leaveRequest)
            ->causedBy(auth()->user())
            ->log('Rejected leave request for: ' . $staff->full_name);

        return back()->with('success', 'Leave request rejected.');
    }

    // ==========================================================
    // ATTENDANCE MANAGEMENT
    // ==========================================================

    public function attendance(Staff $staff, Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $attendance = $staff->attendance()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        $summary = [
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'half_day' => $attendance->where('status', 'half-day')->count(),
            'leave' => $attendance->where('status', 'leave')->count(),
            'holiday' => $attendance->where('status', 'holiday')->count(),
        ];

        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $workingDays = 0;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($year, $month, $i);
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        return view('admin.staff.attendance', compact('staff', 'attendance', 'month', 'year', 'summary', 'workingDays'));
    }

    public function storeAttendance(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half-day,holiday,leave',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = StaffAttendance::where('staff_id', $staff->id)
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Attendance already recorded for this date.');
        }

        $hoursWorked = null;
        if ($validated['clock_in'] && $validated['clock_out']) {
            $clockIn = Carbon::parse($validated['clock_in']);
            $clockOut = Carbon::parse($validated['clock_out']);
            $hoursWorked = $clockIn->diffInHours($clockOut, true);
        }

        $validated['hours_worked'] = $hoursWorked;
        $validated['staff_id'] = $staff->id;
        $validated['marked_by'] = auth()->id();

        $attendance = StaffAttendance::create($validated);

        activity()
            ->performedOn($attendance)
            ->causedBy(auth()->user())
            ->log('Marked attendance for: ' . $staff->full_name . ' (' . $validated['status'] . ')');

        return back()->with('success', 'Attendance recorded successfully!');
    }

    // ==========================================================
    // PAYROLL MANAGEMENT
    // ==========================================================

    public function payroll(Staff $staff, Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $payrolls = $staff->payrolls()
            ->when($month && $year, function ($query) use ($month, $year) {
                return $query->where('month', $month)->where('year', $year);
            })
            ->latest()
            ->paginate(12);

        return view('admin.staff.payroll', compact('staff', 'payrolls', 'month', 'year'));
    }

    public function generatePayroll(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'month' => 'required|string|max:20',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'basic_salary' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'pension' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $existing = StaffPayroll::where('staff_id', $staff->id)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Payroll already generated for this period.');
        }

        $validated['staff_id'] = $staff->id;
        $validated['payroll_period'] = $validated['month'] . ' ' . $validated['year'];
        $validated['created_by'] = auth()->id();

        if (!isset($validated['basic_salary']) || $validated['basic_salary'] <= 0) {
            $validated['basic_salary'] = $staff->basic_salary;
        }

        $payroll = new StaffPayroll($validated);
        $payroll->calculateNetPay();
        $payroll->save();

        activity()
            ->performedOn($payroll)
            ->causedBy(auth()->user())
            ->log('Generated payroll for: ' . $staff->full_name . ' (' . $validated['month'] . ' ' . $validated['year'] . ')');

        return back()->with('success', 'Payroll generated successfully! Net Pay: ₦' . number_format($payroll->net_pay, 2));
    }

    public function processPayroll(Staff $staff, StaffPayroll $payroll)
    {
        if ($payroll->payment_status !== 'pending') {
            return back()->with('error', 'This payroll has already been processed.');
        }

        $payroll->update([
            'payment_status' => 'processed',
            'processed_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($payroll)
            ->causedBy(auth()->user())
            ->log('Processed payroll for: ' . $staff->full_name);

        return back()->with('success', 'Payroll processed successfully!');
    }

    public function markPayrollPaid(Request $request, Staff $staff, StaffPayroll $payroll)
    {
        if ($payroll->payment_status === 'paid') {
            return back()->with('error', 'This payroll is already marked as paid.');
        }

        $validated = $request->validate([
            'transaction_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
        ]);

        $payroll->update([
            'payment_status' => 'paid',
            'payment_date' => $validated['payment_date'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
        ]);

        activity()
            ->performedOn($payroll)
            ->causedBy(auth()->user())
            ->log('Marked payroll as paid for: ' . $staff->full_name);

        return back()->with('success', 'Payroll marked as paid!');
    }

    // ==========================================================
    // PERFORMANCE REVIEWS
    // ==========================================================

    public function performanceReviews(Staff $staff)
    {
        $reviews = $staff->performanceReviews()->latest()->paginate(10);
        return view('admin.staff.performance-reviews', compact('staff', 'reviews'));
    }

    public function storePerformanceReview(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'review_date' => 'required|date',
            'review_period' => 'required|string|max:50',
            'punctuality' => 'nullable|integer|min:1|max:5',
            'productivity' => 'nullable|integer|min:1|max:5',
            'teamwork' => 'nullable|integer|min:1|max:5',
            'communication' => 'nullable|integer|min:1|max:5',
            'technical_skills' => 'nullable|integer|min:1|max:5',
            'leadership' => 'nullable|integer|min:1|max:5',
            'problem_solving' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string|max:1000',
            'areas_for_improvement' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000',
            'reviewer_comments' => 'nullable|string|max:1000',
        ]);

        $validated['staff_id'] = $staff->id;
        $validated['reviewer_id'] = auth()->id();
        $validated['status'] = 'submitted';

        $review = StaffPerformanceReview::create($validated);
        $review->calculateOverallRating();
        $review->save();

        activity()
            ->performedOn($review)
            ->causedBy(auth()->user())
            ->log('Created performance review for: ' . $staff->full_name);

        return back()->with('success', 'Performance review submitted successfully!');
    }

    public function approvePerformanceReview(Staff $staff, StaffPerformanceReview $review)
    {
        if ($review->status === 'approved') {
            return back()->with('error', 'This review is already approved.');
        }

        $review->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        activity()
            ->performedOn($review)
            ->causedBy(auth()->user())
            ->log('Approved performance review for: ' . $staff->full_name);

        return back()->with('success', 'Performance review approved!');
    }

    // ==========================================================
    // BULK ACTIONS
    // ==========================================================

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
            'action' => 'required|in:activate,deactivate,suspend,terminate,delete',
        ]);

        $count = 0;
        $failed = 0;

        foreach ($validated['staff_ids'] as $staffId) {
            $staff = Staff::find($staffId);
            if ($staff) {
                try {
                    if ($validated['action'] === 'delete') {
                        if ($staff->classAssigned || $staff->subjects()->count() > 0) {
                            $failed++;
                            continue;
                        }
                        $staff->delete();
                    } else {
                        $statusMap = [
                            'activate' => 'active',
                            'deactivate' => 'inactive',
                            'suspend' => 'suspended',
                            'terminate' => 'terminated',
                        ];
                        $staff->status = $statusMap[$validated['action']];
                        $staff->save();
                    }
                    $count++;
                } catch (\Exception $e) {
                    $failed++;
                    Log::error('Bulk action failed for staff ' . $staffId . ': ' . $e->getMessage());
                }
            }
        }

        $message = "Action performed on {$count} staff members.";
        if ($failed > 0) {
            $message .= " {$failed} failed.";
        }

        return back()->with('success', $message);
    }
}
