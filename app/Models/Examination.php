<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id', 'class_arm_id', 'subject_id', 'term_id', 'academic_year_id',
        'name', 'exam_date', 'ca_deadline', 'total_marks',
        'ca_weight', 'exam_weight', 'status', 'settings', 'created_by'
    ];

    protected $casts = [
        'exam_date' => 'date',
        'ca_deadline' => 'date',
        'settings' => 'array',
        'ca_weight' => 'integer',
        'exam_weight' => 'integer'
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

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    // Methods
    public function getStudents()
    {
        return $this->class->students()
            ->whereHas('enrollments', function ($q) {
                $q->where('class_id', $this->class_id)
                  ->where('class_arm_id', $this->class_arm_id)
                  ->where('term_id', $this->term_id)
                  ->where('academic_year_id', $this->academic_year_id)
                  ->where('status', 'active');
            })
            ->get();
    }

    public function getGradeForStudent($studentId)
    {
        return $this->grades()->where('student_id', $studentId)->first();
    }

    public function getStudentCount()
    {
        return $this->getStudents()->count();
    }

    public function isComplete()
    {
        $totalStudents = $this->getStudentCount();
        $gradedStudents = $this->grades()->count();
        return $totalStudents > 0 && $gradedStudents >= $totalStudents;
    }

    public function getStatusLabel()
    {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
            'completed' => 'Completed',
            'archived' => 'Archived'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return [
            'draft' => 'gray',
            'published' => 'blue',
            'completed' => 'green',
            'archived' => 'red'
        ][$this->status] ?? 'gray';
    }
}