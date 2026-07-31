<?php

namespace App\Services;

use App\Models\Parent as ParentModel;
use App\Models\Student;
use App\Models\ExeatRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Term;
use App\Mail\ParentVerification;
use App\Mail\ParentWelcome;
use App\Mail\ExeatRequestSubmitted;
use App\Mail\ExeatApproved;
use App\Mail\ExeatRejected;
use App\Mail\InvoiceNotification;
use App\Mail\PaymentConfirmation;
use App\Mail\GradePublished;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send parent verification email
     */
    public function sendVerificationEmail(ParentModel $parent): void
    {
        try {
            Mail::to($parent->email)->send(new ParentVerification($parent));
            Log::info('Verification email sent to: ' . $parent->email);
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(ParentModel $parent): void
    {
        try {
            Mail::to($parent->email)->send(new ParentWelcome($parent));
            Log::info('Welcome email sent to: ' . $parent->email);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    /**
     * Send exeat request submitted notification
     */
    public function sendExeatSubmitted(ExeatRequest $exeat): void
    {
        try {
            Mail::to($exeat->parent->email)->send(new ExeatRequestSubmitted($exeat));
            
            // Also notify school admin (if needed)
            // $this->notifyAdminExeatSubmitted($exeat);
            
            Log::info('Exeat submitted notification sent to: ' . $exeat->parent->email);
        } catch (\Exception $e) {
            Log::error('Failed to send exeat submitted notification: ' . $e->getMessage());
        }
    }

    /**
     * Send exeat approved notification
     */
    public function sendExeatApproved(ExeatRequest $exeat): void
    {
        try {
            Mail::to($exeat->parent->email)->send(new ExeatApproved($exeat));
            Log::info('Exeat approved notification sent to: ' . $exeat->parent->email);
        } catch (\Exception $e) {
            Log::error('Failed to send exeat approved notification: ' . $e->getMessage());
        }
    }

    /**
     * Send exeat rejected notification
     */
    public function sendExeatRejected(ExeatRequest $exeat): void
    {
        try {
            Mail::to($exeat->parent->email)->send(new ExeatRejected($exeat));
            Log::info('Exeat rejected notification sent to: ' . $exeat->parent->email);
        } catch (\Exception $e) {
            Log::error('Failed to send exeat rejected notification: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice notification
     */
    public function sendInvoiceNotification(Invoice $invoice): void
    {
        try {
            $parent = $this->getParentForStudent($invoice->student_id);
            if ($parent) {
                Mail::to($parent->email)->send(new InvoiceNotification($invoice));
                Log::info('Invoice notification sent to: ' . $parent->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send invoice notification: ' . $e->getMessage());
        }
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(Payment $payment): void
    {
        try {
            $parent = $this->getParentForStudent($payment->student_id);
            if ($parent) {
                Mail::to($parent->email)->send(new PaymentConfirmation($payment));
                Log::info('Payment confirmation sent to: ' . $parent->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Send grade published notification
     */
    public function sendGradePublished(Student $student, Term $term): void
    {
        try {
            $parent = $this->getParentForStudent($student->id);
            if ($parent) {
                Mail::to($parent->email)->send(new GradePublished($student, $term));
                Log::info('Grade published notification sent to: ' . $parent->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send grade published notification: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk notifications to all parents
     */
    public function sendBulkNotification(string $subject, string $message, array $parentIds): void
    {
        foreach ($parentIds as $parentId) {
            try {
                $parent = ParentModel::find($parentId);
                if ($parent && $parent->email_notifications) {
                    // Use a generic notification email
                    Mail::to($parent->email)->send(new \App\Mail\GenericNotification($subject, $message, $parent));
                    Log::info('Bulk notification sent to: ' . $parent->email);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send bulk notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get parent for a student
     */
    private function getParentForStudent($studentId): ?ParentModel
    {
        $student = Student::with('parents')->find($studentId);
        return $student?->parents->first();
    }

    /**
     * Get all active parents
     */
    public function getAllActiveParents()
    {
        return ParentModel::where('status', 'active')
            ->where('email_notifications', true)
            ->get();
    }
}