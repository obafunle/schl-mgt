<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'short_name', 'category', 'level',
        'weekly_hours', 'exam_weight', 'ca_weight',
        'description', 'is_active', 'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function staffSubjects()
    {
        return $this->hasMany(StaffSubject::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Methods
    public function getFullName()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    public function getGradeLetter($score)
    {
        // Nigerian grading system
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        if ($score >= 30) return 'E';
        return 'F';
    }

    public function getGradeRemark($score)
    {
        if ($score >= 70) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 50) return 'Fair';
        if ($score >= 40) return 'Pass';
        if ($score >= 30) return 'Poor';
        return 'Fail';
    }
}
