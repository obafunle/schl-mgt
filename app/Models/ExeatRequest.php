<?php

/**
 * Exeat Request Model
 *
 * This model handles student permission/exeat requests made by parents.
 * It tracks the entire lifecycle of a request from submission to completion.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExeatRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using the create() or update() methods.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exeat_number',      // Unique identifier for the request (e.g., EXE-2024-ABC123)
        'student_id',        // The student this exeat is for
        'parent_id',         // The parent who made the request
        'term_id',           // The academic term
        'academic_year_id',  // The academic year
        'departure_date',    // Date the student will leave
        'return_date',       // Date the student will return
        'departure_time',    // Optional: Time of departure
        'return_time',       // Optional: Time of return
        'reason',            // Why the student needs to leave
        'destination',       // Where the student is going
        'accompanied_by',    // Who is accompanying the student
        'contact_during_absence', // Contact person/number during absence
        'status',            // pending, approved, rejected, cancelled, completed
        'approved_by',       // The admin who approved/rejected the request
        'approved_at',       // When the request was approved/rejected
        'rejection_reason',  // If rejected, why
        'departure_confirmed_at', // When the student actually departed
        'return_confirmed_at',    // When the student actually returned
        'meta',              // Additional data as JSON
        'created_by'         // Who created this record
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'departure_date' => 'date',           // Cast to Carbon date object
        'return_date' => 'date',              // Cast to Carbon date object
        'departure_time' => 'datetime',       // Cast to Carbon datetime object
        'return_time' => 'datetime',          // Cast to Carbon datetime object
        'approved_at' => 'datetime',          // Cast to Carbon datetime object
        'departure_confirmed_at' => 'datetime',
        'return_confirmed_at' => 'datetime',
        'meta' => 'array'                     // JSON fields become arrays
    ];

    /* ============================================================
       RELATIONSHIPS
       ============================================================ */

    /**
     * Get the student associated with this exeat request.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the parent who made this exeat request.
     */
    public function parent()
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    /**
     * Get the academic term for this exeat request.
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the academic year for this exeat request.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the admin who approved or rejected this request.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created this record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ============================================================
       ACCESSORS & MUTATORS
       ============================================================ */

    /**
     * Get the human-readable status label.
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed'
        ][$this->status] ?? $this->status;
    }

    /**
     * Get the status color for UI badges.
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending'   => 'yellow',
            'approved'  => 'green',
            'rejected'  => 'red',
            'cancelled' => 'gray',
            'completed' => 'blue'
        ][$this->status] ?? 'gray';
    }

    /**
     * Calculate the total number of days for this exeat.
     */
    public function getDaysDifferenceAttribute()
    {
        if ($this->departure_date && $this->return_date) {
            return $this->departure_date->diffInDays($this->return_date) + 1;
        }
        return 0;
    }

    /* ============================================================
       SCOPES (Query Filters)
       ============================================================ */

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include requests for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include requests for a specific parent.
     */
    public function scopeForParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /* ============================================================
       HELPER METHODS
       ============================================================ */

    /**
     * Check if this request is still pending approval.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this request has been approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if this request has been rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if this request has been completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get the QR code data for this exeat (for verification).
     */
    public function getQRCodeData()
    {
        return [
            'exeat_number' => $this->exeat_number,
            'student'      => $this->student->full_name ?? 'Unknown',
            'admission'    => $this->student->admission_number ?? 'N/A',
            'departure'    => $this->departure_date?->format('Y-m-d'),
            'return'       => $this->return_date?->format('Y-m-d'),
            'status'       => $this->status
        ];
    }

    /**
     * Approve this exeat request.
     * Sets status to 'approved' and records who approved it.
     */
    public function approve()
    {
        $this->status = 'approved';
        $this->approved_by = auth()->id();
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Reject this exeat request with a reason.
     */
    public function reject($reason)
    {
        $this->status = 'rejected';
        $this->approved_by = auth()->id();
        $this->approved_at = now();
        $this->rejection_reason = $reason;
        $this->save();
    }

    /**
     * Mark this exeat as completed (student has returned).
     */
    public function complete()
    {
        $this->status = 'completed';
        $this->return_confirmed_at = now();
        $this->save();
    }

    /**
     * Cancel this exeat request (before approval).
     */
    public function cancel()
    {
        if ($this->isPending()) {
            $this->status = 'cancelled';
            $this->save();
        }
    }
}
