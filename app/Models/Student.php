<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'email',
        'photo',
        'class_id',          // ✅ This is the correct column name
        'class_arm',
        'academic_year_id',
        'parent_name',
        'parent_phone',
        'parent_email',
        'status',
        'meta',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'meta' => 'array'
    ];

    // ============================================================
    // RELATIONSHIPS - ✅ ALL FIXED WITH EXPLICIT FOREIGN KEYS
    // ============================================================

    /**
     * Get the class that this student belongs to.
     * ✅ FIXED: Explicitly using 'class_id' as foreign key
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the class arm that this student belongs to.
     */
    public function classArm()
    {
        return $this->belongsTo(ClassArm::class, 'class_arm_id');
    }

    /**
     * Get the academic year for this student.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who created this student record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the grades for this student.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the payments for this student.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the attendance records for this student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the enrollments for this student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the hostel assignments for this student.
     */
    public function hostelAssignments()
    {
        return $this->hasMany(HostelBedAssignment::class);
    }

    /**
     * Get the library borrowings for this student.
     */
    public function libraryBorrowings()
    {
        return $this->hasMany(LibraryBorrowing::class);
    }

    /**
     * Get the transport assignments for this student.
     */
    public function transportAssignments()
    {
        return $this->hasMany(TransportAssignment::class);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get the student's full name including middle name.
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    /**
     * Get the student's photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }
        $name = urlencode($this->full_name ?? 'Student');
        return "https://ui-avatars.com/api/?name={$name}&size=100&background=4F46E5&color=fff&bold=true";
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include students in a specific class.
     */
    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to only include students in a specific arm.
     */
    public function scopeInArm($query, $classArm)
    {
        return $query->where('class_arm', $classArm);
    }

    /**
     * Scope a query to find a student by admission number.
     */
    public function scopeByAdmissionNumber($query, $admissionNumber)
    {
        return $query->where('admission_number', $admissionNumber);
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Check if the student has an active hostel assignment.
     */
    public function hasActiveHostelAssignment()
    {
        return $this->hostelAssignments()->where('status', 'active')->exists();
    }

    /**
     * Get the student's current hostel.
     */
    public function getCurrentHostel()
    {
        $assignment = $this->hostelAssignments()->where('status', 'active')->first();
        return $assignment ? $assignment->hostel : null;
    }

    /**
     * Get the student's current room.
     */
    public function getCurrentRoom()
    {
        $assignment = $this->hostelAssignments()->where('status', 'active')->first();
        return $assignment ? $assignment->room : null;
    }

    /**
     * Get the student's current transport assignment.
     */
    public function getCurrentTransportAssignment()
    {
        return $this->transportAssignments()->where('status', 'active')->first();
    }
}
