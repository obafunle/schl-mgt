<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'class_subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'class_arm_id',
        'subject_id',
        'teacher_id',
        'weekly_hours',
        'is_core',
        'is_compulsory',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_core' => 'boolean',
        'is_compulsory' => 'boolean',
        'weekly_hours' => 'integer',
    ];

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    /**
     * Get the class (e.g., JSS 1) associated with this assignment.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    /**
     * Get the class arm (e.g., A, B) associated with this assignment.
     */
    public function classArm()
    {
        return $this->belongsTo(ClassArm::class);
    }

    /**
     * Get the subject associated with this assignment.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher (staff) associated with this assignment.
     */
    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    /**
     * Get the user who created this assignment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================================
    // SCOPES
    // ==========================================================

    /**
     * Scope a query to only include core subjects.
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope a query to only include compulsory subjects.
     */
    public function scopeCompulsory($query)
    {
        return $query->where('is_compulsory', true);
    }

    /**
     * Scope a query to only include assignments for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to only include assignments for a specific teacher.
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // ==========================================================
    // ACCESSORS
    // ==========================================================

    /**
     * Get a human-readable label for the assignment.
     */
    public function getAssignmentLabelAttribute()
    {
        $label = $this->subject->name ?? 'Unknown Subject';
        $label .= ' - ' . ($this->class->name ?? 'Unknown Class');
        if ($this->classArm) {
            $label .= ' (' . $this->classArm->name . ')';
        }
        return $label;
    }
}
