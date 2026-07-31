<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSubject extends Model
{
    use HasFactory;

    protected $table = 'staff_subjects';

    protected $fillable = [
        'staff_id', 'subject_id', 'class_id', 'role', 'weekly_hours', 'is_active', 'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weekly_hours' => 'integer'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
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

    // Methods
    public function getRoleLabel()
    {
        return [
            'primary' => 'Primary',
            'secondary' => 'Secondary',
            'assistant' => 'Assistant'
        ][$this->role] ?? $this->role;
    }
}