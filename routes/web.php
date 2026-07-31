<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ExaminationController;
use App\Http\Controllers\Admin\FeeStructureController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\HostelController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\ExeatController;
use App\Http\Controllers\Parent\ParentPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route (for authenticated users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================
// ADMIN ROUTES (All require authentication)
// ============================================================
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================================
    // STUDENT MANAGEMENT
    // ==========================================================
    Route::resource('students', StudentController::class);

    // ==========================================================
    // STAFF MANAGEMENT
    // ==========================================================
    Route::resource('staff', StaffController::class);
    Route::post('staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggle-status');

    // Staff Leave
    Route::prefix('staff/{staff}')->group(function () {
        Route::get('leave-requests', [StaffController::class, 'leaveRequests'])->name('staff.leave-requests');
        Route::post('leave-requests', [StaffController::class, 'storeLeaveRequest'])->name('staff.store-leave-request');
        Route::post('leave-requests/{leaveRequest}/approve', [StaffController::class, 'approveLeaveRequest'])->name('staff.approve-leave-request');
        Route::post('leave-requests/{leaveRequest}/reject', [StaffController::class, 'rejectLeaveRequest'])->name('staff.reject-leave-request');
    });

    // Staff Attendance
    Route::prefix('staff/{staff}')->group(function () {
        Route::get('attendance', [StaffController::class, 'attendance'])->name('staff.attendance');
        Route::post('attendance', [StaffController::class, 'storeAttendance'])->name('staff.store-attendance');
    });

    // Staff Payroll
    Route::prefix('staff/{staff}')->group(function () {
        Route::get('payroll', [StaffController::class, 'payroll'])->name('staff.payroll');
        Route::post('payroll', [StaffController::class, 'generatePayroll'])->name('staff.generate-payroll');
        Route::post('payroll/{payroll}/process', [StaffController::class, 'processPayroll'])->name('staff.process-payroll');
        Route::post('payroll/{payroll}/mark-paid', [StaffController::class, 'markPayrollPaid'])->name('staff.mark-payroll-paid');
    });

    // Staff Performance Reviews
    Route::prefix('staff/{staff}')->group(function () {
        Route::get('performance-reviews', [StaffController::class, 'performanceReviews'])->name('staff.performance-reviews');
        Route::post('performance-reviews', [StaffController::class, 'storePerformanceReview'])->name('staff.store-performance-review');
        Route::post('performance-reviews/{review}/approve', [StaffController::class, 'approvePerformanceReview'])->name('staff.approve-performance-review');
    });

    // Staff Bulk Actions
    Route::post('staff/bulk-action', [StaffController::class, 'bulkAction'])->name('staff.bulk-action');

    // ==========================================================
    // CLASS MANAGEMENT
    // ==========================================================
    Route::resource('classes', ClassController::class);
    Route::post('classes/{class}/add-arm', [ClassController::class, 'addArm'])->name('classes.add-arm');
    Route::post('classes/{class}/assign-subject', [ClassController::class, 'assignSubject'])->name('classes.assign-subject');
    Route::delete('classes-subject/{classSubject}', [ClassController::class, 'removeSubject'])->name('classes.remove-subject');

    // ==========================================================
    // SUBJECT MANAGEMENT
    // ==========================================================
    Route::resource('subjects', SubjectController::class);
    Route::post('subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])->name('subjects.toggle-status');
    Route::post('subjects/bulk-action', [SubjectController::class, 'bulkAction'])->name('subjects.bulk-action');
    Route::get('get-subjects', [SubjectController::class, 'getSubjects'])->name('get-subjects');
    Route::get('subjects/by-category/{category}', [SubjectController::class, 'byCategory'])->name('subjects.by-category');
    Route::get('subjects/by-level/{level}', [SubjectController::class, 'byLevel'])->name('subjects.by-level');
    // ==========================================================
    // ACADEMIC YEAR & TERM
    // ==========================================================
    Route::resource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');

    // ==========================================================
    // EXAMINATION & GRADING
    // ==========================================================
    Route::resource('examinations', ExaminationController::class);
    Route::get('examinations/{examination}/enter-grades', [ExaminationController::class, 'enterGrades'])->name('examinations.enter-grades');
    Route::post('examinations/{examination}/store-grades', [ExaminationController::class, 'storeGrades'])->name('examinations.store-grades');
    Route::post('examinations/{examination}/calculate-positions', [ExaminationController::class, 'calculatePositions'])->name('examinations.calculate-positions');
    Route::post('examinations/{examination}/approve-grades', [ExaminationController::class, 'approveGrades'])->name('examinations.approve-grades');
    Route::post('examinations/{examination}/publish-results', [ExaminationController::class, 'publishResults'])->name('examinations.publish-results');

    // ==========================================================
    // FEE & FINANCIAL MANAGEMENT
    // ==========================================================
    Route::resource('fees', FeeStructureController::class);
    Route::post('fees/{fee}/toggle-active', [FeeStructureController::class, 'toggleActive'])->name('fees.toggle-active');
    Route::post('fees/{fee}/clone', [FeeStructureController::class, 'clone'])->name('fees.clone');
    Route::post('fees/bulk-delete', [FeeStructureController::class, 'bulkDelete'])->name('fees.bulk-delete');

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/bulk-generate', [InvoiceController::class, 'generateBulk'])->name('invoices.bulk-generate');

    // ==========================================================
    // HOSTEL MANAGEMENT
    // ==========================================================
    Route::resource('hostels', HostelController::class);
    Route::get('hostels/{hostel}/rooms', [HostelController::class, 'rooms'])->name('hostels.rooms');
    Route::post('hostels/{hostel}/rooms', [HostelController::class, 'storeRoom'])->name('hostels.rooms.store');
    Route::post('hostels/{hostel}/assign', [HostelController::class, 'assignStudent'])->name('hostels.assign');
    Route::post('assignments/{assignment}/release', [HostelController::class, 'releaseStudent'])->name('hostels.release');
    Route::get('hostels/{hostel}/attendance', [HostelController::class, 'attendance'])->name('hostels.attendance');
    Route::post('hostels/{hostel}/attendance', [HostelController::class, 'markAttendance'])->name('hostels.attendance.mark');
    Route::get('hostels/{hostel}/complaints', [HostelController::class, 'complaints'])->name('hostels.complaints');
    Route::get('hostels/{hostel}/fees', [HostelController::class, 'fees'])->name('hostels.fees');
    Route::get('hostels/students-available', [HostelController::class, 'getStudents'])->name('hostels.students.available');

    // ==========================================================
    // TIMETABLE MANAGEMENT
    // ==========================================================
    Route::resource('timetable', TimetableController::class);
    Route::get('timetable/show', [TimetableController::class, 'show'])->name('timetable.show');
    Route::post('timetable/generate', [TimetableController::class, 'generate'])->name('timetable.generate');
    Route::post('timetable/resolve-conflicts/{entry}', [TimetableController::class, 'resolveConflicts'])->name('timetable.resolve-conflicts');
    Route::post('timetable/swap', [TimetableController::class, 'swap'])->name('timetable.swap');
    Route::post('timetable/move', [TimetableController::class, 'move'])->name('timetable.move');
    Route::post('timetable/clone', [TimetableController::class, 'clone'])->name('timetable.clone');
    Route::get('timetable/export', [TimetableController::class, 'export'])->name('timetable.export');
    Route::get('timetable/teacher', [TimetableController::class, 'teacherTimetable'])->name('timetable.teacher');
    Route::get('timetable/room', [TimetableController::class, 'roomTimetable'])->name('timetable.room');

    // ==========================================================
    // TRANSPORT MANAGEMENT
    // ==========================================================
    Route::resource('transport', TransportController::class);
    Route::post('transport/{transport}/assign', [TransportController::class, 'assignStudent'])->name('transport.assign');
    Route::post('transport/assignments/{assignment}/release', [TransportController::class, 'releaseStudent'])->name('transport.release');

    // ==========================================================
    // LIBRARY MANAGEMENT
    // ==========================================================
    Route::resource('library', LibraryController::class);
    Route::get('library/borrow', [LibraryController::class, 'borrow'])->name('library.borrow');
    Route::post('library/borrow', [LibraryController::class, 'storeBorrow'])->name('library.borrow.store');
    Route::post('library/borrowings/{borrowing}/return', [LibraryController::class, 'returnBook'])->name('library.return');
    Route::post('library/borrowings/{borrowing}/calculate-fine', [LibraryController::class, 'calculateFine'])->name('library.calculate-fine');

    // ==========================================================
    // INVENTORY MANAGEMENT
    // ==========================================================
    Route::resource('inventory', InventoryController::class);
    Route::post('inventory/{item}/stock', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
    Route::post('inventory/{item}/remove-stock', [InventoryController::class, 'removeStock'])->name('inventory.remove-stock');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');

    // ==========================================================
    // PARENT MANAGEMENT (Admin)
    // ==========================================================
    Route::resource('parents', ParentController::class);
    Route::post('parents/{parent}/toggle-status', [ParentController::class, 'toggleStatus'])->name('parents.toggle-status');

    // ==========================================================
    // REPORTS & ANALYTICS
    // ==========================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');  // ← THIS WAS MISSING
        Route::get('/academic', [ReportController::class, 'academic'])->name('academic');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/hostel', [ReportController::class, 'hostel'])->name('hostel');
        Route::get('/library', [ReportController::class, 'library'])->name('library');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // ==========================================================
    // EXEAT MANAGEMENT (Admin)
    // ==========================================================
    Route::prefix('exeats')->name('exeats.')->group(function () {
        Route::get('/', [ExeatController::class, 'index'])->name('index');
        Route::get('{exeat}', [ExeatController::class, 'show'])->name('show');
        Route::post('{exeat}/approve', [ExeatController::class, 'approve'])->name('approve');
        Route::post('{exeat}/reject', [ExeatController::class, 'reject'])->name('reject');
        Route::post('{exeat}/complete', [ExeatController::class, 'markCompleted'])->name('complete');
        Route::post('{exeat}/remind', [ExeatController::class, 'sendReminder'])->name('remind');
        Route::post('bulk-action', [ExeatController::class, 'bulkAction'])->name('bulk-action');
    });

    // ==========================================================
    // SYSTEM UPDATES
    // ==========================================================
    Route::prefix('updates')->name('updates.')->group(function () {
        Route::get('/', [UpdateController::class, 'index'])->name('index');
        Route::post('/check', [UpdateController::class, 'check'])->name('check');
        Route::post('/install', [UpdateController::class, 'install'])->name('install');
        Route::get('/status', [UpdateController::class, 'status'])->name('status');
    });

    // ==========================================================
    // AJAX/API ENDPOINTS
    // ==========================================================
    Route::get('get-class-arms', [ClassController::class, 'getArms'])->name('get-class-arms');
    Route::get('get-students', [StudentController::class, 'getStudents'])->name('get-students');
    Route::get('get-staff', [StaffController::class, 'getStaff'])->name('get-staff');
    Route::get('get-subjects', [SubjectController::class, 'getSubjects'])->name('get-subjects');

}); // END ADMIN ROUTES

// ============================================================
// PARENT PORTAL ROUTES (Authenticated)
// ============================================================
Route::prefix('parent')->name('parent.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');

    // Children
    Route::get('/children', [ParentPortalController::class, 'children'])->name('children');
    Route::get('/children/{childId}', [ParentPortalController::class, 'childProfile'])->name('child.profile');
    Route::get('/children/{childId}/grades', [ParentPortalController::class, 'childGrades'])->name('child.grades');
    Route::get('/children/{childId}/fees', [ParentPortalController::class, 'childFees'])->name('fees');
    Route::get('/children/{childId}/attendance', [ParentPortalController::class, 'childAttendance'])->name('child.attendance');

    // Invoice Payment
    Route::get('/pay-invoice/{invoiceId}', [ParentPortalController::class, 'payInvoice'])->name('pay.invoice');

    // Paystack Callback
    Route::get('/paystack/callback', [ParentPortalController::class, 'paystackCallback'])->name('paystack.callback');

    // Exeat Management
    Route::get('/exeats', [ParentPortalController::class, 'exeats'])->name('exeats');
    Route::post('/exeats', [ParentPortalController::class, 'storeExeat'])->name('exeats.store');
    Route::post('/exeats/{exeat}/cancel', [ParentPortalController::class, 'cancelExeat'])->name('exeats.cancel');
    Route::get('/exeats/{exeat}', [ParentPortalController::class, 'exeatDetails'])->name('exeats.details');
    Route::get('/exeats/{exeat}/download', [ParentPortalController::class, 'downloadExeat'])->name('exeats.download');

    // Profile
    Route::get('/profile', [ParentPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [ParentPortalController::class, 'updateProfile'])->name('profile.update');

}); // END PARENT ROUTES

// Auth routes (generated by Laravel Breeze/Jetstream)
require __DIR__.'/auth.php';
