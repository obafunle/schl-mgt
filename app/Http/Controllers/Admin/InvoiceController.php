<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->middleware('permission:view_fees');
        $this->middleware('permission:create_fees')->only(['create', 'store', 'generateBulk']);
        $this->middleware('permission:edit_fees')->only(['edit', 'update']);
        $this->middleware('permission:delete_fees')->only(['destroy']);
        $this->middleware('permission:process_payments')->only(['pay', 'processPayment', 'callback']);

        $this->paystackService = $paystackService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'class', 'classArm', 'term', 'academicYear']);

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('term_id') && $request->term_id) {
            $query->where('term_id', $request->term_id);
        }

        $invoices = $query->latest()->paginate(20);
        $classes = ClassModel::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();

        return view('admin.invoices.index', compact('invoices', 'classes', 'terms'));
    }

    /**
     * Show the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'class', 'classArm', 'term', 'academicYear', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        $students = Student::where('status', 'active')->get();
        $classes = ClassModel::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('admin.invoices.create', compact('students', 'classes', 'terms', 'academicYears'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $class = $student->class;
        $classArm = $student->classArm;

        $subtotal = collect($validated['items'])->sum('amount');
        $discount = $validated['discount'] ?? 0;
        $total = $subtotal - $discount;

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'student_id' => $validated['student_id'],
            'class_id' => $class->id,
            'class_arm_id' => $classArm->id ?? null,
            'term_id' => $validated['term_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'issue_date' => now(),
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'amount_paid' => 0,
            'balance' => $total,
            'items' => $validated['items'],
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully! Invoice #' . $invoice->invoice_number);
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Cannot edit a sent or paid invoice.');
        }

        $students = Student::where('status', 'active')->get();
        $classes = ClassModel::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('admin.invoices.edit', compact('invoice', 'students', 'classes', 'terms', 'academicYears'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Cannot edit a sent or paid invoice.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $subtotal = collect($validated['items'])->sum('amount');
        $discount = $validated['discount'] ?? 0;
        $total = $subtotal - $discount;

        $invoice->update([
            'student_id' => $validated['student_id'],
            'class_id' => $student->class_id,
            'class_arm_id' => $student->class_arm_id,
            'term_id' => $validated['term_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'balance' => $total - $invoice->amount_paid,
            'items' => $validated['items'],
        ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Cannot delete a sent or paid invoice.');
        }

        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Send invoice to parent
     */
    public function send(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already paid.');
        }

        $invoice->status = 'sent';
        $invoice->save();

        // TODO: Send email notification
        // Mail::to($invoice->student->email)->send(new InvoiceNotification($invoice));

        return back()->with('success', 'Invoice sent to parent successfully!');
    }

    /**
     * Generate bulk invoices for a class
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'due_date' => 'required|date|after:today',
        ]);

        // Get all active students in the class
        $students = Student::where('class_id', $request->class_id)
            ->where('status', 'active')
            ->when($request->class_arm_id, function ($q) use ($request) {
                return $q->where('class_arm_id', $request->class_arm_id);
            })
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found in this class.');
        }

        // Get fee structures for this class
        $fees = FeeStructure::active()
            ->forClass($request->class_id, $request->class_arm_id)
            ->get();

        if ($fees->isEmpty()) {
            return back()->with('error', 'No active fee structures found for this class.');
        }

        DB::transaction(function () use ($students, $fees, $request) {
            foreach ($students as $student) {
                // Check if invoice already exists
                $existingInvoice = Invoice::where('student_id', $student->id)
                    ->where('term_id', $request->term_id)
                    ->where('academic_year_id', $request->academic_year_id)
                    ->whereIn('status', ['draft', 'sent', 'partial'])
                    ->first();

                if ($existingInvoice) {
                    continue;
                }

                // Prepare items from fee structures
                $items = [];
                $subtotal = 0;
                foreach ($fees as $fee) {
                    $items[] = [
                        'name' => $fee->name,
                        'amount' => $fee->amount,
                        'code' => $fee->code,
                        'frequency' => $fee->frequency,
                    ];
                    $subtotal += $fee->amount;
                }

                // Create invoice
                Invoice::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'student_id' => $student->id,
                    'class_id' => $request->class_id,
                    'class_arm_id' => $request->class_arm_id,
                    'term_id' => $request->term_id,
                    'academic_year_id' => $request->academic_year_id,
                    'issue_date' => now(),
                    'due_date' => $request->due_date,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'total' => $subtotal,
                    'amount_paid' => 0,
                    'balance' => $subtotal,
                    'items' => $items,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Bulk invoices generated successfully!');
    }

    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('Y') . '-';
        $lastInvoice = Invoice::where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Process payment for an invoice via Paystack
     */
    public function pay(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice has already been paid.');
        }

        $student = $invoice->student;
        $email = $student->email ?? $student->parent_email ?? 'parent@example.com';
        $amount = $invoice->balance;
        
        if ($amount <= 0) {
            return back()->with('error', 'Invoice balance is zero or negative.');
        }

        $result = $this->paystackService->initializeTransaction([
            'amount' => $amount,
            'email' => $email,
            'reference' => $this->paystackService->generateReference(),
            'callback_url' => route('paystack.callback'),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        if (!$result['success']) {
            return back()->with('error', 'Payment initialization failed: ' . $result['message']);
        }

        // Save payment reference
        $invoice->payment_reference = $result['reference'];
        $invoice->save();

        // Redirect to Paystack payment page
        return redirect($result['authorization_url']);
    }

    /**
     * Handle Paystack callback after payment
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        
        if (!$reference) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Invalid payment reference.');
        }

        // Verify the transaction
        $result = $this->paystackService->verifyTransaction($reference);

        if (!$result['success']) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Payment verification failed: ' . $result['message']);
        }

        if ($result['status'] !== 'success') {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Payment was not successful.');
        }

        // Find the invoice
        $invoice = Invoice::where('payment_reference', $reference)->first();
        
        if (!$invoice) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Invoice not found.');
        }

        // Check if payment already processed (prevent duplicates)
        $existingPayment = Payment::where('reference', $reference)->first();
        if ($existingPayment) {
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('info', 'This payment has already been processed.');
        }

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

        // Log success
        \Illuminate\Support\Facades\Log::info('Payment successful', [
            'invoice' => $invoice->invoice_number,
            'amount' => $result['amount'],
            'reference' => $reference,
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Payment of ₦' . number_format($result['amount'], 2) . ' was successful!');
    }

    /**
     * Process manual payment (cash, bank transfer, etc.)
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->balance,
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already fully paid.');
        }

        $amount = $request->amount;

        DB::transaction(function () use ($invoice, $amount, $request) {
            // Create payment record
            Payment::create([
                'transaction_id' => 'MANUAL-' . time() . '-' . Str::random(6),
                'reference' => $request->reference ?? 'MANUAL-' . time(),
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $amount,
                'fee_charged' => 0,
                'payment_method' => $request->payment_method,
                'gateway' => 'manual',
                'status' => 'success',
                'payment_date' => now(),
                'verified_at' => now(),
                'processed_by' => auth()->id(),
                'meta' => ['notes' => $request->notes],
            ]);

            // Update invoice
            $invoice->amount_paid += $amount;
            $invoice->balance = $invoice->total - $invoice->amount_paid;
            
            if ($invoice->balance <= 0) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();
        });

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Manual payment of ₦' . number_format($amount, 2) . ' recorded successfully!');
    }
}