<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'level',
        'category',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    public function arms()
    {
        return $this->hasMany(ClassArm::class, 'class_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, "class_id");
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getFullNameAttribute()
    {
        return "{$this->name} ({$this->code})";
    }

    public function getCategoryLabelAttribute()
    {
        return [
            'primary' => 'Primary',
            'junior' => 'Junior Secondary',
            'senior' => 'Senior Secondary',
        ][$this->category] ?? $this->category;
    }

    public function getCategoryColorAttribute()
    {
        return [
            'primary' => 'green',
            'junior' => 'blue',
            'senior' => 'purple',
        ][$this->category] ?? 'gray';
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ============================================================
    // METHODS
    // ============================================================

    public function getTotalStudents()
    {
        return $this->students()->count();
    }

    public function getTotalArms()
    {
        return $this->arms()->count();
    }

    public function getActiveArms()
    {
        return $this->arms()->where('is_active', true)->get();
    }

    public function hasArm($armName)
    {
        return $this->arms()->where('name', $armName)->exists();
    }

    public function getStatistics()
    {
        $arms = $this->arms;
        $totalStudents = $this->students()->count();
        $totalArms = $arms->count();
        $activeArms = $arms->where('is_active', true)->count();

        return [
            'total_students' => $totalStudents,
            'total_arms' => $totalArms,
            'active_arms' => $activeArms,
            'subjects' => $this->classSubjects()->count(),
        ];
    }
}
