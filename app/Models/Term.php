<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'term_number',
        'start_date',
        'end_date',
        'exam_start_date',
        'exam_end_date',
        'result_publication_date',
        'is_active',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'exam_start_date' => 'date',
        'exam_end_date' => 'date',
        'result_publication_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' - ' . ($this->academicYear->name ?? 'N/A');
    }
}
