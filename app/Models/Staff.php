<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_id', 'first_name', 'last_name', 'middle_name',
        'date_of_birth', 'gender', 'email', 'phone', 'address', 'photo',
        'staff_type', 'employment_type', 'hire_date', 'termination_date',
        'qualifications', 'experience',
        'basic_salary', 'allowances', 'deductions',
        'bank_name', 'bank_account_number', 'bank_account_name',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_relationship',
        'status', 'meta', 'created_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'qualifications' => 'array',
        'experience' => 'array',
        'meta' => 'array',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'staff_subjects')
                    ->withPivot('class_id', 'role', 'weekly_hours', 'is_active')
                    ->withTimestamps();
    }

    public function classSubjects()
    {
        return $this->hasMany(StaffSubject::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(StaffLeaveRequest::class);
    }

    public function attendance()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(StaffPayroll::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(StaffPerformanceReview::class);
    }

    public function classAssigned()
    {
        return $this->hasOne(ClassArm::class, 'class_teacher_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('images/default-avatar.png');
    }

    public function getQualificationListAttribute()
    {
        if (empty($this->qualifications)) {
            return [];
        }
        return collect($this->qualifications)->map(function ($qual) {
            return $qual['degree'] . ' (' . $qual['institution'] . ', ' . $qual['year'] . ')';
        })->toArray();
    }

    public function getExperienceSummaryAttribute()
    {
        if (empty($this->experience)) {
            return [];
        }
        return collect($this->experience)->map(function ($exp) {
            return $exp['position'] . ' at ' . $exp['school'] . ' (' . $exp['years'] . ' years)';
        })->toArray();
    }

    public function getTotalExperienceAttribute()
    {
        if (empty($this->experience)) {
            return 0;
        }
        return collect($this->experience)->sum('years');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTeachers($query)
    {
        return $query->where('staff_type', 'teacher');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('staff_type', $type);
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTeacher()
    {
        return $this->staff_type === 'teacher';
    }

    public function getStaffTypeLabel()
    {
        return [
            'teacher' => 'Teacher',
            'admin' => 'Administrator',
            'support' => 'Support Staff',
            'accountant' => 'Accountant',
            'librarian' => 'Librarian',
            'other' => 'Other'
        ][$this->staff_type] ?? $this->staff_type;
    }

    public function getEmploymentTypeLabel()
    {
        return [
            'permanent' => 'Permanent',
            'contract' => 'Contract',
            'part-time' => 'Part-Time',
            'intern' => 'Intern'
        ][$this->employment_type] ?? $this->employment_type;
    }

    public function getStatusLabel()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'terminated' => 'Terminated'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'yellow',
            'terminated' => 'red'
        ][$this->status] ?? 'gray';
    }
}
