<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'examination_id', 'subject_id', 'class_id', 'class_arm_id',
        'term_id', 'academic_year_id',
        'ca_score', 'exam_score', 'total_score',
        'ca_weighted', 'exam_weighted',
        'grade', 'remark', 'position', 'total_students',
        'entered_by', 'entered_at', 'approved_by', 'approved_at', 'meta'
    ];

    protected $casts = [
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'ca_weighted' => 'decimal:2',
        'exam_weighted' => 'decimal:2',
        'entered_at' => 'datetime',
        'approved_at' => 'datetime',
        'meta' => 'array'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function classArm()
    {
        return $this->belongsTo(ClassArm::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Methods
    public function calculateGrade($score = null)
    {
        $score = $score ?? $this->total_score;
        
        // Nigerian Grading System
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        if ($score >= 30) return 'E';
        return 'F';
    }

    public function calculateRemark($score = null)
    {
        $score = $score ?? $this->total_score;
        
        if ($score >= 70) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 50) return 'Fair';
        if ($score >= 40) return 'Pass';
        if ($score >= 30) return 'Poor';
        return 'Fail';
    }

    public function calculateTotal()
    {
        if ($this->ca_score !== null && $this->exam_score !== null) {
            $this->ca_weighted = ($this->ca_score / 100) * $this->examination->ca_weight;
            $this->exam_weighted = ($this->exam_score / 100) * $this->examination->exam_weight;
            $this->total_score = $this->ca_weighted + $this->exam_weighted;
            
            $this->grade = $this->calculateGrade();
            $this->remark = $this->calculateRemark();
        }
        
        return $this;
    }

    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    public function getGradeColor()
    {
        return [
            'A' => 'green',
            'B' => 'blue',
            'C' => 'yellow',
            'D' => 'orange',
            'E' => 'red',
            'F' => 'darkred'
        ][$this->grade] ?? 'gray';
    }

    public function getGradePoint()
    {
        return [
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.5,
            'F' => 0.0
        ][$this->grade] ?? 0;
    }
}