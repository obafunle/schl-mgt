<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian as ParentModel;
use App\Models\Student;
use App\Models\ExeatRequest;
use App\Models\Invoice;
use App\Models\Grade;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ReportCard;
use App\Models\Payment;
use App\Services\PaystackService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ParentPortalController extends Controller
{
    protected $paystackService;
    protected $notificationService;

    public function __construct(PaystackService $paystackService, NotificationService $notificationService)
    {
        $this->middleware('auth')->except(['register', 'storeRegistration', 'verifyEmail']);
        $this->middleware('guest')->only(['register', 'storeRegistration']);
        $this->paystackService = $paystackService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show parent registration form
     */
    public function register()
    {
        return view('parent.auth.register');
    }

    /**
     * Store parent registration
     */
    public function storeRegistration(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:parents,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
            'child_admission_number' => 'required|string|exists:students,admission_number',
            'relationship' => 'required|in:father,mother,guardian,other',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:255',
        ]);

        // Find the student
        $student = Student::where('admission_number', $validated['child_admission_number'])->first();

        if (!$student) {
            return back()->with('error', 'Student not found. Please check the admission number.')
                ->withInput();
        }

        // Check if student already has a parent
        $existingParent = ParentModel::whereHas('children', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->first();

        if ($existingParent) {
            return back()->with('error', 'This student already has a registered parent. Please contact the school.')
                ->withInput();
        }

        $parent = null;

        DB::transaction(function () use ($validated, $student, &$parent) {
            // Create user account
            $user = \App\Models\User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->assignRole('parent');

            // Create parent profile
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'status' => 'active',
                'verification_code' => Str::random(60),
                'created_by' => auth()->id(),
            ]);

            // Link child
            $parent->children()->attach($student->id, [
                'relationship' => $validated['relationship'],
                'is_primary_contact' => true,
                'can_receive_notifications' => true,
            ]);

            // Send verification email
            $this->notificationService->sendVerificationEmail($parent);
        });

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please check your email (' . $validated['email'] . ') to verify your account.');
    }

    /**
     * Verify parent email
     */
    public function verifyEmail($code)
    {
        $parent = ParentModel::where('verification_code', $code)->first();

        if (!$parent) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification code. Please contact support.');
        }

        $parent->update([
            'email_verified_at' => now(),
            'verification_code' => null,
        ]);

        // Send welcome email
        $this->notificationService->sendWelcomeEmail($parent);

        // Create session message
        session()->flash('success', 'Email verified successfully! You can now login.');

        return redirect()->route('login');
    }

    /**
     * Show parent dashboard
     */
    public function dashboard()
    {
        $parent = Auth::user()->parent;

        if (!$parent) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Parent profile not found.');
        }

        $children = $parent->children()->with(['class', 'classArm'])->get();

        // Get current term and academic year
        $currentTerm = Term::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get notifications
        $notifications = $this->getParentNotifications($parent);

        // Get summary statistics
        $stats = [
            'total_children' => $children->count(),
            'pending_exeats' => $parent->exeatRequests()->pending()->count(),
            'unpaid_invoices' => $this->getUnpaidInvoicesCount($children),
            'upcoming_exams' => $this->getUpcomingExams($children),
        ];

        // Get recent activities
        $recentActivities = $this->getRecentActivities($children);

        // Get children with their grades for the dashboard
        foreach ($children as $child) {
            $child->grades = Grade::where('student_id', $child->id)
                ->where('term_id', $currentTerm?->id)
                ->where('academic_year_id', $currentAcademicYear?->id)
                ->get();
        }

        return view('parent.dashboard', compact(
            'parent',
            'children',
            'stats',
            'notifications',
            'recentActivities',
            'currentTerm',
            'currentAcademicYear'
        ));
    }

    /**
     * Show children list
     */
    public function children()
    {
        $parent = Auth::user()->parent;
        $children = $parent->children()->with(['class', 'classArm', 'grades' => function ($q) {
            $q->where('term_id', Term::where('is_current', true)->first()?->id);
        }])->get();

        $currentTerm = Term::where('is_current', true)->first();

        return view('parent.children.index', compact('parent', 'children', 'currentTerm'));
    }

    /**
     * Show child profile
     */
    public function childProfile($childId)
    {
        $parent = Auth::user()->parent;

        $child = Student::with(['class', 'classArm', 'grades.subject', 'attendances'])
            ->whereHas('parents', function ($q) use ($parent) {
                $q->where('parent_id', $parent->id);
            })
            ->findOrFail($childId);

        // Get current term and academic year
        $currentTerm = Term::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get grades
        $grades = Grade::where('student_id', $child->id)
            ->where('term_id', $currentTerm?->id)
            ->where('academic_year_id', $currentAcademicYear?->id)
            ->with(['subject', 'examination'])
            ->get();

        // Calculate summary
        $summary = [
            'total_score' => $grades->sum('total_score'),
            'average' => $grades->count() > 0 ? $grades->avg('total_score') : 0,
            'subjects' => $grades->count(),
            'passed' => $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count(),
            'failed' => $grades->where('grade', 'F')->count(),
        ];

        // Get attendance summary
        $attendanceSummary = $this->getAttendanceSummary($child);

        // Get invoices
        $invoices = Invoice::where('student_id', $child->id)
            ->with(['term', 'academicYear'])
            ->latest()
            ->limit(5)
            ->get();

        // Get exeat requests
        $exeats = ExeatRequest::where('student_id', $child->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('parent.children.profile', compact(
            'child',
            'grades',
            'summary',
            'attendanceSummary',
            'invoices',
            'exeats',
            'currentTerm',
            'currentAcademicYear'
        ));
    }

    /**
     * Show child grades
     */
    public function childGrades($childId, Request $request)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        $selectedTerm = $request->get('term_id', $terms->first()?->id);
        $selectedYear = $request->get('academic_year_id', AcademicYear::where('is_current', true)->first()?->id);

        $grades = Grade::where('student_id', $child->id)
            ->when($selectedTerm, function ($q) use ($selectedTerm) {
                return $q->where('term_id', $selectedTerm);
            })
            ->when($selectedYear, function ($q) use ($selectedYear) {
                return $q->where('academic_year_id', $selectedYear);
            })
            ->with(['subject', 'examination'])
            ->get();

        $reportCard = ReportCard::where('student_id', $child->id)
            ->where('term_id', $selectedTerm)
            ->where('academic_year_id', $selectedYear)
            ->first();

        $summary = [
            'total_score' => $grades->sum('total_score'),
            'average' => $grades->count() > 0 ? $grades->avg('total_score') : 0,
            'subjects' => $grades->count(),
            'passed' => $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count(),
            'failed' => $grades->where('grade', 'F')->count(),
        ];

        return view('parent.children.grades', compact(
            'child',
            'grades',
            'terms',
            'academicYears',
            'selectedTerm',
            'selectedYear',
            'reportCard',
            'summary'
        ));
    }

    /**
     * Show child fees
     */
    public function childFees($childId)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $invoices = Invoice::where('student_id', $child->id)
            ->with(['term', 'academicYear', 'payments'])
            ->latest()
            ->paginate(10);

        $totalInvoiced = $invoices->sum('total');
        $totalPaid = $invoices->sum('amount_paid');
        $balance = $totalInvoiced - $totalPaid;

        return view('parent.children.fees', compact(
            'child',
            'invoices',
            'totalInvoiced',
            'totalPaid',
            'balance'
        ));
    }

    /**
     * Pay invoice online
     */
    public function payInvoice($invoiceId)
    {
        $invoice = Invoice::with(['student'])->findOrFail($invoiceId);
        $parent = Auth::user()->parent;

        // Verify parent owns this invoice
        if (!$parent->children->contains($invoice->student_id)) {
            abort(403, 'You are not authorized to pay this invoice.');
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already paid.');
        }

        $amount = $invoice->balance;
        if ($amount <= 0) {
            return back()->with('error', 'No balance remaining on this invoice.');
        }

        $email = $parent->email;

        $result = $this->paystackService->initializeTransaction([
            'amount' => $amount,
            'email' => $email,
            'reference' => $this->paystackService->generateReference(),
            'callback_url' => route('parent.paystack.callback'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'parent_id' => $parent->id,
                'payment_type' => 'invoice',
                'parent_email' => $email,
                'student_name' => $invoice->student->full_name,
            ],
        ]);

        if (!$result['success']) {
            Log::error('Payment initialization failed', [
                'invoice_id' => $invoice->id,
                'error' => $result['message']
            ]);
            return back()->with('error', 'Payment initialization failed: ' . $result['message']);
        }

        // Save payment reference
        $invoice->payment_reference = $result['reference'];
        $invoice->save();

        // Redirect to Paystack payment page
        return redirect($result['authorization_url']);
    }

    /**
     * Paystack callback handler
     */
    public function paystackCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Invalid payment reference.');
        }

        // Verify the transaction
        $result = $this->paystackService->verifyTransaction($reference);

        if (!$result['success']) {
            Log::error('Paystack verification failed', [
                'reference' => $reference,
                'response' => $result
            ]);
            return redirect()->route('parent.dashboard')
                ->with('error', 'Payment verification failed: ' . $result['message']);
        }

        if ($result['status'] !== 'success') {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Payment was not successful. Status: ' . $result['status']);
        }

        // Find invoice
        $invoice = Invoice::where('payment_reference', $reference)->first();

        if (!$invoice) {
            Log::error('Invoice not found for payment', ['reference' => $reference]);
            return redirect()->route('parent.dashboard')
                ->with('error', 'Invoice not found for this payment.');
        }

        // Check if payment already processed (prevent duplicates)
        $existingPayment = Payment::where('reference', $reference)->first();
        if ($existingPayment) {
            return redirect()->route('parent.fees', $invoice->student_id)
                ->with('info', 'This payment has already been processed.');
        }

        DB::transaction(function () use ($result, $invoice, $reference) {
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => $result['reference'],
                'reference' => $reference,
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $result['amount'],
                'fee_charged' => 0,
                'payment_method' => 'card',
                'gateway' => 'paystack',
                'paystack_reference' => $reference,
                'paystack_authorization_code' => $result['authorization_code'] ?? null,
                'paystack_response' => json_encode($result),
                'status' => 'success',
                'payment_date' => now(),
                'verified_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Update invoice
            $invoice->amount_paid += $result['amount'];
            $invoice->balance = $invoice->total - $invoice->amount_paid;

            if ($invoice->balance <= 0) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            // Send payment confirmation notification
            $this->notificationService->sendPaymentConfirmation($payment);
        });

        return redirect()->route('parent.fees', $invoice->student_id)
            ->with('success', 'Payment of ₦' . number_format($result['amount'], 2) . ' was successful! A confirmation email has been sent.');
    }

    /**
     * Show child attendance
     */
    public function childAttendance($childId, Request $request)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $attendance = Attendance::where('student_id', $child->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        // Calculate summary
        $summary = [
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'excused' => $attendance->where('status', 'excused')->count(),
            'total' => $attendance->count(),
        ];

        $summary['percentage'] = $summary['total'] > 0
            ? round(($summary['present'] / $summary['total']) * 100, 2)
            : 0;

        return view('parent.children.attendance', compact(
            'child',
            'attendance',
            'summary',
            'month',
            'year'
        ));
    }

    /**
     * Show exeat requests
     */
    public function exeats()
    {
        $parent = Auth::user()->parent;
        $exeats = $parent->exeatRequests()
            ->with(['student', 'term', 'academicYear'])
            ->latest()
            ->paginate(10);

        $children = $parent->children;
        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('parent.exeats.index', compact(
            'exeats',
            'children',
            'terms',
            'academicYears'
        ));
    }

    /**
     * Create exeat request
     */
    public function storeExeat(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date',
            'departure_time' => 'nullable|date_format:H:i',
            'return_time' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:1000',
            'destination' => 'nullable|string|max:255',
            'accompanied_by' => 'nullable|string|max:255',
            'contact_during_absence' => 'nullable|string|max:255',
        ]);

        $parent = Auth::user()->parent;

        // Verify parent owns this child
        if (!$parent->children->contains($validated['student_id'])) {
            abort(403, 'You are not authorized to request exeat for this student.');
        }

        $student = Student::find($validated['student_id']);
        $currentTerm = Term::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if (!$currentTerm || !$currentAcademicYear) {
            return back()->with('error', 'No active term or academic year found. Please contact the school.');
        }

        // Check if student already has a pending exeat
        $existingExeat = ExeatRequest::where('student_id', $validated['student_id'])
            ->where('status', 'pending')
            ->exists();

        if ($existingExeat) {
            return back()->with('error', 'This student already has a pending exeat request.');
        }

        $validated['exeat_number'] = 'EXE-' . date('Y') . '-' . strtoupper(Str::random(6));
        $validated['parent_id'] = $parent->id;
        $validated['term_id'] = $currentTerm->id;
        $validated['academic_year_id'] = $currentAcademicYear->id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        $exeat = null;

        DB::transaction(function () use ($validated, &$exeat) {
            $exeat = ExeatRequest::create($validated);

            // Send notification
            $this->notificationService->sendExeatSubmitted($exeat);
        });

        return redirect()->route('parent.exeats')
            ->with('success', 'Exeat request submitted successfully! You will receive a notification once approved.');
    }

    /**
     * Cancel exeat request
     */
    public function cancelExeat(ExeatRequest $exeat)
    {
        $parent = Auth::user()->parent;

        if ($exeat->parent_id !== $parent->id) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        if ($exeat->status !== 'pending') {
            return back()->with('error', 'Only pending exeat requests can be cancelled.');
        }

        $exeat->update(['status' => 'cancelled']);

        return back()->with('success', 'Exeat request cancelled successfully.');
    }

    /**
     * Show exeat details
     */
    public function exeatDetails(ExeatRequest $exeat)
    {
        $parent = Auth::user()->parent;

        if ($exeat->parent_id !== $parent->id) {
            abort(403, 'You are not authorized to view this request.');
        }

        $exeat->load(['student', 'term', 'academicYear', 'approvedBy']);

        return view('parent.exeats.details', compact('exeat'));
    }

    /**
     * Download exeat PDF
     */
    public function downloadExeat(ExeatRequest $exeat)
    {
        $parent = Auth::user()->parent;

        if ($exeat->parent_id !== $parent->id) {
            abort(403, 'You are not authorized to download this request.');
        }

        if ($exeat->status !== 'approved') {
            return back()->with('error', 'Only approved exeat requests can be downloaded.');
        }

        // Generate PDF
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('parent.exeats.pdf', compact('exeat'));
            return $pdf->download('exeat-' . $exeat->exeat_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

    /**
     * Show parent profile
     */
    public function profile()
    {
        $parent = Auth::user()->parent;
        return view('parent.profile', compact('parent'));
    }

    /**
     * Update parent profile
     */
    public function updateProfile(Request $request)
    {
        $parent = Auth::user()->parent;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($parent->photo) {
                Storage::disk('public')->delete($parent->photo);
            }
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['sms_notifications'] = $request->has('sms_notifications');

        $parent->update($validated);

        // Update user name
        $user = Auth::user();
        $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Get parent notifications
     */
    private function getParentNotifications($parent)
    {
        $notifications = collect();

        // Pending exeat requests
        $pendingExeats = $parent->exeatRequests()->pending()->count();
        if ($pendingExeats > 0) {
            $notifications->push([
                'type' => 'exeat',
                'message' => "You have {$pendingExeats} pending exeat request(s) awaiting approval.",
                'icon' => '📋',
                'link' => route('parent.exeats'),
                'priority' => 'high',
            ]);
        }

        // Unpaid invoices
        foreach ($parent->children as $child) {
            $unpaid = Invoice::where('student_id', $child->id)
                ->whereIn('status', ['sent', 'partial', 'overdue'])
                ->sum('balance');

            if ($unpaid > 0) {
                $notifications->push([
                    'type' => 'fee',
                    'message' => "Balance of ₦" . number_format($unpaid, 2) . " due for {$child->full_name}.",
                    'icon' => '💰',
                    'link' => route('parent.fees', $child->id),
                    'priority' => 'high',
                ]);
            }
        }

        // Upcoming exams
        $upcomingExams = $this->getUpcomingExams($parent->children);
        if ($upcomingExams > 0) {
            $notifications->push([
                'type' => 'exam',
                'message' => "{$upcomingExams} exam(s) coming up soon. Check the academic calendar.",
                'icon' => '📝',
                'link' => '#',
                'priority' => 'medium',
            ]);
        }

        // Approved exeats
        $approvedExeats = $parent->exeatRequests()->where('status', 'approved')->where('approved_at', '>=', now()->subDays(7))->count();
        if ($approvedExeats > 0) {
            $notifications->push([
                'type' => 'exeat_approved',
                'message' => "You have {$approvedExeats} recently approved exeat request(s).",
                'icon' => '✅',
                'link' => route('parent.exeats'),
                'priority' => 'medium',
            ]);
        }

        return $notifications->sortByDesc('priority')->take(5);
    }

    /**
     * Get unpaid invoices count
     */
    private function getUnpaidInvoicesCount($children)
    {
        $count = 0;
        foreach ($children as $child) {
            $count += Invoice::where('student_id', $child->id)
                ->whereIn('status', ['sent', 'partial', 'overdue'])
                ->count();
        }
        return $count;
    }

    /**
     * Get upcoming exams count
     */
    private function getUpcomingExams($children)
    {
        $count = 0;
        foreach ($children as $child) {
            // Check for any upcoming examinations
            $count += \App\Models\Examination::whereHas('class', function ($q) use ($child) {
                $q->where('id', $child->class_id);
            })->where('exam_date', '>=', now())
                ->count();
        }
        return $count;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($children)
    {
        $activities = collect();

        foreach ($children as $child) {
            // Get recent grades
            $grades = Grade::where('student_id', $child->id)
                ->whereNotNull('entered_at')
                ->orderBy('entered_at', 'desc')
                ->limit(2)
                ->with(['subject'])
                ->get();

            foreach ($grades as $grade) {
                $activities->push([
                    'child' => $child->full_name,
                    'activity' => "New grade in {$grade->subject->name}: {$grade->grade} ({$grade->total_score}%)",
                    'date' => $grade->entered_at,
                    'icon' => '📊',
                    'type' => 'grade',
                ]);
            }

            // Get recent payments
            $payments = Payment::where('student_id', $child->id)
                ->where('status', 'success')
                ->orderBy('payment_date', 'desc')
                ->limit(1)
                ->get();

            foreach ($payments as $payment) {
                $activities->push([
                    'child' => $child->full_name,
                    'activity' => "Payment of ₦" . number_format($payment->amount, 2) . " received for invoice #{$payment->invoice->invoice_number ?? 'N/A'}",
                    'date' => $payment->payment_date,
                    'icon' => '💳',
                    'type' => 'payment',
                ]);
            }

            // Get recent exeat approvals
            $exeats = ExeatRequest::where('student_id', $child->id)
                ->where('status', 'approved')
                ->whereNotNull('approved_at')
                ->orderBy('approved_at', 'desc')
                ->limit(1)
                ->get();

            foreach ($exeats as $exeat) {
                $activities->push([
                    'child' => $child->full_name,
                    'activity' => "Exeat request #{$exeat->exeat_number} approved",
                    'date' => $exeat->approved_at,
                    'icon' => '✅',
                    'type' => 'exeat',
                ]);
            }
        }

        return $activities->sortByDesc('date')->take(10);
    }

    /**
     * Get attendance summary
     */
    private function getAttendanceSummary($child)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $attendance = Attendance::where('student_id', $child->id)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->get();

        return [
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'excused' => $attendance->where('status', 'excused')->count(),
            'total' => $attendance->count(),
            'percentage' => $attendance->count() > 0
                ? round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Upload photo
     */
    private function uploadPhoto($photo)
    {
        $filename = time() . '-' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('parents/photos', $filename, 'public');

        try {
            // Resize image using Intervention Image
            $image = \Intervention\Image\Facades\Image::make(Storage::disk('public')->path($path));
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
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:parents,email',
        ]);

        $parent = ParentModel::where('email', $request->email)->first();

        if (!$parent) {
            return back()->with('error', 'Parent not found.');
        }

        if ($parent->hasVerifiedEmail()) {
            return back()->with('info', 'This email is already verified.');
        }

        // Generate new verification code
        $parent->verification_code = Str::random(60);
        $parent->save();

        // Send verification email
        $this->notificationService->sendVerificationEmail($parent);

        return back()->with('success', 'Verification email resent! Please check your inbox.');
    }

    /**
     * Get child report card
     */
    public function getReportCard($childId, Request $request)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $termId = $request->get('term_id');
        $academicYearId = $request->get('academic_year_id');

        if (!$termId || !$academicYearId) {
            return back()->with('error', 'Please select a term and academic year.');
        }

        $reportCard = ReportCard::where('student_id', $child->id)
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if (!$reportCard) {
            return back()->with('error', 'Report card not found for the selected period.');
        }

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.report-card', compact('reportCard', 'child'));
            return $pdf->download('report-card-' . $child->admission_number . '-' . $termId . '.pdf');
        } catch (\Exception $e) {
            Log::error('Report card PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate report card. Please try again.');
        }
    }

    /**
     * Check if parent has verified email
     */
    public function checkVerification(Request $request)
    {
        $parent = ParentModel::where('email', $request->email)->first();

        if (!$parent) {
            return response()->json(['verified' => false, 'message' => 'Parent not found.']);
        }

        return response()->json([
            'verified' => $parent->hasVerifiedEmail(),
            'message' => $parent->hasVerifiedEmail()
                ? 'Email verified.'
                : 'Email not verified. Please check your inbox.',
        ]);
    }

    /**
     * Get child's current term grades
     */
    public function getCurrentGrades($childId)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $currentTerm = Term::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if (!$currentTerm || !$currentAcademicYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active term or academic year found.'
            ]);
        }

        $grades = Grade::where('student_id', $child->id)
            ->where('term_id', $currentTerm->id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->with(['subject'])
            ->get();

        $summary = [
            'total_score' => $grades->sum('total_score'),
            'average' => $grades->count() > 0 ? $grades->avg('total_score') : 0,
            'subjects' => $grades->count(),
            'passed' => $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count(),
            'failed' => $grades->where('grade', 'F')->count(),
        ];

        return response()->json([
            'success' => true,
            'grades' => $grades,
            'summary' => $summary,
        ]);
    }

    /**
     * Get child's invoice summary
     */
    public function getInvoiceSummary($childId)
    {
        $parent = Auth::user()->parent;

        $child = Student::whereHas('parents', function ($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->findOrFail($childId);

        $invoices = Invoice::where('student_id', $child->id)->get();

        $summary = [
            'total_invoiced' => $invoices->sum('total'),
            'total_paid' => $invoices->sum('amount_paid'),
            'balance' => $invoices->sum('total') - $invoices->sum('amount_paid'),
            'total_invoices' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'unpaid_invoices' => $invoices->whereIn('status', ['sent', 'partial', 'overdue'])->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }
}
