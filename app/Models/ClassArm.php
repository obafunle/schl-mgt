<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassArm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'class_arms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'name',
        'code',
        'capacity',
        'current_enrollment',
        'class_teacher_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'current_enrollment' => 'integer',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the class that this arm belongs to.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the teacher assigned as class teacher for this arm.
     */
    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    /**
     * Get the subjects assigned to this class arm.
     */
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_arm_id');
    }

    /**
     * Get the students in this class arm.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_arm_id');
    }

    /**
     * Get the enrollments for this class arm.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_arm_id');
    }

    /**
     * Get the timetable entries for this class arm.
     */
    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class, 'class_arm_id');
    }

    /**
     * Get the examinations for this class arm.
     */
    public function examinations()
    {
        return $this->hasMany(Examination::class, 'class_arm_id');
    }

    /**
     * Get the staff subjects for this class arm.
     */
    public function staffSubjects()
    {
        return $this->hasMany(StaffSubject::class, 'class_arm_id');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get the full name of the class arm (Class Name + Arm Name).
     */
    public function getFullNameAttribute()
    {
        if ($this->class) {
            return $this->class->name . ' ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Get the class arm code with class code.
     */
    public function getFullCodeAttribute()
    {
        if ($this->class) {
            return $this->class->code . $this->code;
        }
        return $this->code;
    }

    /**
     * Get the occupancy rate as a percentage.
     */
    public function getOccupancyRateAttribute()
    {
        if ($this->capacity == 0) {
            return 0;
        }
        return round(($this->current_enrollment / $this->capacity) * 100, 2);
    }

    /**
     * Get the number of available slots in this class arm.
     */
    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->capacity - $this->current_enrollment);
    }

    /**
     * Get the status label for display.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the status color for badges.
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'green' : 'gray';
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to only include active class arms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include class arms with available capacity.
     */
    public function scopeHasCapacity($query)
    {
        return $query->whereRaw('current_enrollment < capacity');
    }

    /**
     * Scope a query to only include full class arms.
     */
    public function scopeFull($query)
    {
        return $query->whereRaw('current_enrollment >= capacity');
    }

    /**
     * Scope a query to only include class arms for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Check if the class arm is full.
     */
    public function isFull()
    {
        return $this->current_enrollment >= $this->capacity;
    }

    /**
     * Check if the class arm has available slots.
     */
    public function hasAvailableSlots()
    {
        return $this->current_enrollment < $this->capacity;
    }

    /**
     * Update the current enrollment count.
     */
    public function updateEnrollment()
    {
        $this->current_enrollment = $this->students()->count();
        $this->save();
    }

    /**
     * Increment enrollment by 1.
     */
    public function incrementEnrollment()
    {
        $this->current_enrollment = $this->current_enrollment + 1;
        $this->save();
    }

    /**
     * Decrement enrollment by 1.
     */
    public function decrementEnrollment()
    {
        if ($this->current_enrollment > 0) {
            $this->current_enrollment = $this->current_enrollment - 1;
            $this->save();
        }
    }

    /**
     * Get the list of students in this class arm with their details.
     */
    public function getStudentsList()
    {
        return $this->students()
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Get the count of active students in this class arm.
     */
    public function getActiveStudentsCount()
    {
        return $this->students()->where('status', 'active')->count();
    }

    /**
     * Get the class arm statistics.
     */
    public function getStatistics()
    {
        $totalStudents = $this->students()->count();
        $activeStudents = $this->students()->where('status', 'active')->count();
        $maleStudents = $this->students()->where('gender', 'male')->count();
        $femaleStudents = $this->students()->where('gender', 'female')->count();

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'male_students' => $maleStudents,
            'female_students' => $femaleStudents,
            'capacity' => $this->capacity,
            'available_slots' => $this->available_slots,
            'occupancy_rate' => $this->occupancy_rate,
            'is_full' => $this->isFull(),
        ];
    }
}
