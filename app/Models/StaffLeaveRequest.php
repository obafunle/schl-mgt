<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'leave_type', 'start_date', 'end_date', 'total_days',
        'reason', 'notes', 'status', 'approved_by', 'approved_at',
        'rejection_reason', 'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Methods
    public function getLeaveTypeLabel()
    {
        return [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
            'compassionate' => 'Compassionate Leave',
            'other' => 'Other'
        ][$this->leave_type] ?? $this->leave_type;
    }

    public function getStatusLabel()
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray'
        ][$this->status] ?? 'gray';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}