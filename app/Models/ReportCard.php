<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'class_id', 'class_arm_id', 'term_id', 'academic_year_id',
        'total_score', 'average_score', 'position', 'total_students', 'grade_point_average',
        'total_subjects', 'subjects_passed', 'subjects_failed',
        'promotion_status', 'principal_remarks', 'class_teacher_remarks',
        'status', 'generated_by', 'published_at'
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'average_score' => 'decimal:2',
        'grade_point_average' => 'decimal:2',
        'published_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
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

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    // Methods
    public function getPromotionStatusLabel()
    {
        return [
            'promoted' => 'Promoted',
            'demoted' => 'Demoted',
            'repeated' => 'Repeated',
            'pending' => 'Pending'
        ][$this->promotion_status] ?? 'Pending';
    }

    public function getPromotionStatusColor()
    {
        return [
            'promoted' => 'green',
            'demoted' => 'red',
            'repeated' => 'orange',
            'pending' => 'gray'
        ][$this->promotion_status] ?? 'gray';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }
}