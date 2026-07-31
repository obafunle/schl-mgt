<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'is_active', 'is_current',
        'description', 'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean'
    ];

    // Relationships
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
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

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // Methods
    public function getCurrentTerm()
    {
        return $this->terms()->where('is_current', true)->first();
    }

    public function getActiveTerm()
    {
        return $this->terms()->where('is_active', true)->first();
    }

    public function getFormattedDateRange()
    {
        return $this->start_date->format('M Y') . ' - ' . $this->end_date->format('M Y');
    }
}