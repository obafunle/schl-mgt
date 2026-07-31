<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimetableEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id', 'class_arm_id', 'subject_id', 'teacher_id',
        'room_id', 'day_id', 'period_id', 'term_id', 'academic_year_id',
        'status', 'notes', 'meta', 'is_recurring',
        'start_date', 'end_date', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'meta' => 'array'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function classArm()
    {
        return $this->belongsTo(ClassArm::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function day()
    {
        return $this->belongsTo(TimetableDay::class);
    }

    public function period()
    {
        return $this->belongsTo(TimetablePeriod::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function conflicts()
    {
        return $this->hasMany(TimetableConflict::class, 'entry_id');
    }

    public function conflictingEntries()
    {
        return $this->hasMany(TimetableConflict::class, 'conflicting_entry_id');
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeByClass($query, $classId, $classArmId = null)
    {
        $query->where('class_id', $classId);
        if ($classArmId) {
            $query->where('class_arm_id', $classArmId);
        }
        return $query;
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByDay($query, $dayId)
    {
        return $query->where('day_id', $dayId);
    }

    public function scopeByPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    // Methods
    public function getStatusLabel()
    {
        return [
            'scheduled' => 'Scheduled',
            'rescheduled' => 'Rescheduled',
            'cancelled' => 'Cancelled',
            'replacement' => 'Replacement'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'scheduled' => 'green',
            'rescheduled' => 'yellow',
            'cancelled' => 'red',
            'replacement' => 'blue'
        ][$this->status] ?? 'gray';
    }

    public function hasConflict()
    {
        return $this->conflicts()->where('is_resolved', false)->exists();
    }

    public function getConflicts()
    {
        return $this->conflicts()->where('is_resolved', false)->get();
    }

    public function getFullDetails()
    {
        return [
            'class' => $this->class->name . ($this->classArm ? ' ' . $this->classArm->name : ''),
            'subject' => $this->subject->name,
            'teacher' => $this->teacher->full_name,
            'room' => $this->room->name ?? 'No Room',
            'day' => $this->day->name,
            'period' => $this->period->name,
            'time' => $this->period->getTimeRange(),
        ];
    }
}